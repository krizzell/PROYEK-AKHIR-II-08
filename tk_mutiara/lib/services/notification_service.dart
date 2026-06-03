import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import '../screens/notification_screen.dart';
import 'api_services.dart';

final FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin =
    FlutterLocalNotificationsPlugin();

AndroidNotificationDetails _paymentAndroidDetails(String? title, String? body) {
  final safeTitle = title ?? 'Pembayaran SPP Berhasil';
  final safeBody =
      body ??
      'Pembayaran SPP Anda berhasil dikonfirmasi. Ketuk untuk melihat detail pembayaran.';

  return AndroidNotificationDetails(
    'payment_channel',
    'Payment Notifications',
    channelDescription: 'Notifikasi status pembayaran',
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
      summaryText: 'TK Mutiara',
    ),
  );
}

@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  print('[BACKGROUND] Message received: ${message.messageId}');

  final RemoteNotification? notif = message.notification;
  if (notif == null) return;

  await flutterLocalNotificationsPlugin.show(
    notif.hashCode,
    notif.title,
    notif.body,
    NotificationDetails(
      android: _paymentAndroidDetails(notif.title, notif.body),
    ),
    payload: message.data.toString(),
  );
  print('[BACKGROUND] Local notification displayed');
}

class NotificationService {
  static final FirebaseMessaging _messaging = FirebaseMessaging.instance;
  static final GlobalKey<NavigatorState> navigatorKey =
      GlobalKey<NavigatorState>();

  static const String _channelId = 'payment_channel';
  static const String _channelName = 'Payment Notifications';
  static const String _channelDesc = 'Notifikasi status pembayaran';

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
        ApiService.notifyPaymentUpdated();
        _openNotificationScreen();
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

      flutterLocalNotificationsPlugin.show(
        notif.hashCode,
        notif.title,
        notif.body,
        NotificationDetails(
          android: _paymentAndroidDetails(notif.title, notif.body),
        ),
        payload: data.toString(),
      );
    });

    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      print('Notification opened from background: ${message.data}');
      if (_isPaymentNotification(message)) {
        ApiService.notifyPaymentUpdated();
        _openNotificationScreen();
      }
    });

    final RemoteMessage? initialMessage = await _messaging.getInitialMessage();
    if (initialMessage != null && _isPaymentNotification(initialMessage)) {
      Future.delayed(const Duration(milliseconds: 500), () {
        print('Notification opened from terminated state');
        ApiService.notifyPaymentUpdated();
        _openNotificationScreen();
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

  static bool _isPaymentNotification(RemoteMessage message) {
    final type = (message.data['type'] ?? '').toString().toLowerCase();
    return type == 'payment_success' ||
        type == 'payment' ||
        type.contains('payment');
  }

  static void _openNotificationScreen() {
    final navigator = navigatorKey.currentState;
    if (navigator == null) {
      print('Navigator belum siap, halaman notifikasi belum bisa dibuka');
      return;
    }

    navigator.push(
      MaterialPageRoute(builder: (_) => const NotificationScreen()),
    );
  }
}
