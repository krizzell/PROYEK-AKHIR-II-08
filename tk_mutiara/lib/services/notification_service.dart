import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import '../screens/notification_screen.dart';
import 'api_services.dart';

final FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin =
    FlutterLocalNotificationsPlugin();

AndroidNotificationDetails _androidDetailsForType(
  String type,
  String? title,
  String? body,
) {
  final isAnnouncement = type.contains('announcement');
  final safeTitle =
      title ??
      (isAnnouncement ? 'Pengumuman Baru' : 'Pengingat Pembayaran SPP');
  final safeBody =
      body ??
      (isAnnouncement
          ? 'Ada pengumuman baru dari TK Mutiara. Ketuk untuk melihat detail.'
          : 'Jangan lupa membayar SPP sebelum tanggal 10 bulan ini.');

  return AndroidNotificationDetails(
    isAnnouncement ? 'announcement_channel' : 'payment_channel',
    isAnnouncement ? 'Pengumuman Sekolah' : 'Payment Notifications',
    channelDescription: isAnnouncement
        ? 'Notifikasi pengumuman terbaru dari sekolah'
        : 'Notifikasi status dan pengingat pembayaran',
    importance: Importance.high,
    priority: Priority.high,
    icon: '@mipmap/ic_launcher',
    color: const Color(0xFFFF6B1A),
    enableVibration: true,
    enableLights: true,
    playSound: true,
    styleInformation: BigTextStyleInformation(
      safeBody,
      contentTitle: safeTitle,
      summaryText: isAnnouncement ? 'Pengumuman TK Mutiara' : 'TK Mutiara',
    ),
  );
}

@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  print('[BACKGROUND] Message received: ${message.messageId}');
}

class NotificationService {
  static final FirebaseMessaging _messaging = FirebaseMessaging.instance;
  static final GlobalKey<NavigatorState> navigatorKey =
      GlobalKey<NavigatorState>();

  static const String _channelId = 'payment_channel';
  static const String _channelName = 'Payment Notifications';
  static const String _channelDesc =
      'Notifikasi status dan pengingat pembayaran';

  static Future<void> init() async {
    print('\n=== INITIALIZING NOTIFICATION SERVICE ===');

    final NotificationSettings settings = await _messaging.requestPermission(
      alert: true,
      badge: true,
      sound: true,
      provisional: false,
    );
    print('Permission status: ${settings.authorizationStatus}');

    const AndroidInitializationSettings androidSettings =
        AndroidInitializationSettings('@mipmap/ic_launcher');
    const InitializationSettings initSettings = InitializationSettings(
      android: androidSettings,
    );

    await flutterLocalNotificationsPlugin.initialize(
      initSettings,
      onDidReceiveNotificationResponse: (NotificationResponse response) {
        print('Local notification tapped: ${response.payload}');
        _openByType(response.payload ?? '');
      },
    );

    final AndroidFlutterLocalNotificationsPlugin? androidImpl =
        flutterLocalNotificationsPlugin
            .resolvePlatformSpecificImplementation<
              AndroidFlutterLocalNotificationsPlugin
            >();

    await androidImpl?.createNotificationChannel(
      const AndroidNotificationChannel(
        _channelId,
        _channelName,
        description: _channelDesc,
        importance: Importance.high,
        enableVibration: true,
        enableLights: true,
        playSound: true,
      ),
    );

    await androidImpl?.createNotificationChannel(
      const AndroidNotificationChannel(
        'announcement_channel',
        'Pengumuman Sekolah',
        description: 'Notifikasi pengumuman terbaru dari sekolah',
        importance: Importance.high,
        enableVibration: true,
        enableLights: true,
        playSound: true,
      ),
    );

    final String? token = await _messaging.getToken();
    if (token != null) {
      print('FCM token ready: ${token.substring(0, 30)}...');
    } else {
      print('FCM token belum tersedia');
    }

    _messaging.onTokenRefresh.listen((String newToken) {
      print('FCM token refreshed: ${newToken.substring(0, 30)}...');
      if (ApiService.token != null) {
        ApiService.saveFcmToken(newToken);
      }
    });

    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      print('[FOREGROUND] Message received: ${message.messageId}');
      final RemoteNotification? notif = message.notification;
      final Map<String, dynamic> data = message.data;

      if (notif == null) return;
      final type = (data['type'] ?? '').toString().toLowerCase();

      flutterLocalNotificationsPlugin.show(
        notif.hashCode,
        notif.title,
        notif.body,
        NotificationDetails(
          android: _androidDetailsForType(type, notif.title, notif.body),
        ),
        payload: type,
      );
    });

    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      print('Notification opened from background: ${message.data}');
      _openByType((message.data['type'] ?? '').toString());
    });

    final RemoteMessage? initialMessage = await _messaging.getInitialMessage();
    if (initialMessage != null) {
      Future.delayed(const Duration(milliseconds: 500), () {
        print('Notification opened from terminated state');
        _openByType((initialMessage.data['type'] ?? '').toString());
      });
    }

    print('=== NOTIFICATION SERVICE INITIALIZED ===\n');
  }

  static Future<void> saveTokenAfterLogin() async {
    print('\n=== SAVING FCM TOKEN AFTER LOGIN ===');
    try {
      final String? fcmToken = await _messaging.getToken();
      if (fcmToken != null) {
        print('FCM token: ${fcmToken.substring(0, 30)}...');
        await ApiService.saveFcmToken(fcmToken);
        print('FCM token saved to server after login');
      } else {
        print('Gagal dapat FCM token dari Firebase');
      }
    } catch (e) {
      print('Error saving FCM token after login: $e');
    }
  }

  static bool _isAnnouncementType(String type) {
    final normalized = type.toLowerCase();
    return normalized == 'announcement' || normalized.contains('announcement');
  }

  static void _openByType(String type) {
    if (_isAnnouncementType(type)) {
      _openNotificationScreen(initialType: 'pengumuman');
      return;
    }

    if (type.toLowerCase().contains('payment')) {
      ApiService.notifyPaymentUpdated();
      _openNotificationScreen(initialType: 'pembayaran');
    }
  }

  static void _openNotificationScreen({String initialType = 'pengumuman'}) {
    final navigator = navigatorKey.currentState;
    if (navigator == null) {
      print('Navigator belum siap, halaman notifikasi belum bisa dibuka');
      return;
    }

    navigator.push(
      MaterialPageRoute(
        builder: (_) => NotificationScreen(initialType: initialType),
      ),
    );
  }
}
