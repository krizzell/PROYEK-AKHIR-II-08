import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'api_services.dart';

@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  print('🔔 [BACKGROUND] Message received: ${message.messageId}');
  
  final RemoteNotification? notif = message.notification;
  if (notif != null) {
    print('🔔 [BACKGROUND] Title: ${notif.title}, Body: ${notif.body}');
    
    // PENTING: Show notification di background juga!
    await flutterLocalNotificationsPlugin.show(
      notif.hashCode, // Unique ID dari notification
      notif.title,
      notif.body,
      const NotificationDetails(
        android: AndroidNotificationDetails(
          'payment_channel',
          'Payment Notifications',
          channelDescription: 'Notifikasi status pembayaran',
          importance: Importance.high,
          priority: Priority.high,
          icon: '@mipmap/ic_launcher',
        ),
      ),
      payload: message.data.toString(),
    );
    print('✓✓ Background notification ditampilkan!');
  }
}

final FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin =
    FlutterLocalNotificationsPlugin();

class NotificationService {
  static final FirebaseMessaging _messaging = FirebaseMessaging.instance;

  static const String _channelId = 'payment_channel';
  static const String _channelName = 'Payment Notifications';
  static const String _channelDesc = 'Notifikasi status pembayaran';

  /// Inisialisasi notification service TANPA save FCM token ke server.
  /// FCM token akan disimpan setelah login via [saveTokenAfterLogin].
  static Future<void> init() async {
    print('\n=== INITIALIZING NOTIFICATION SERVICE ===');
    
    // 1. Request permissions
    print('1️⃣  Requesting notification permissions...');
    NotificationSettings settings = await _messaging.requestPermission(
      alert: true,
      badge: true,
      sound: true,
      provisional: false,
    );
    print('✓ Permission granted: ${settings.authorizationStatus}');

    // 2. Initialize local notifications
    print('2️⃣  Initializing local notifications plugin...');
    const AndroidInitializationSettings androidSettings =
        AndroidInitializationSettings('@mipmap/ic_launcher');

    const InitializationSettings initSettings =
        InitializationSettings(android: androidSettings);

    await flutterLocalNotificationsPlugin.initialize(
      initSettings,
      onDidReceiveNotificationResponse: (NotificationResponse response) {
        print('✓ Notification tapped: ${response.payload}');
      },
    );
    print('✓ Local notifications initialized');

    // 3. Create Android notification channel
    print('3️⃣  Creating Android notification channel...');
    final AndroidFlutterLocalNotificationsPlugin? androidImpl =
        flutterLocalNotificationsPlugin
            .resolvePlatformSpecificImplementation<
                AndroidFlutterLocalNotificationsPlugin>();

    if (androidImpl != null) {
      await androidImpl.createNotificationChannel(
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
      print('✓ Notification channel created');
    } else {
      print('⚠ Android implementation not found');
    }

    // 4. Get FCM token (hanya log, JANGAN save ke server — user belum login)
    print('4️⃣  Getting FCM token...');
    final String? token = await _messaging.getToken();
    if (token != null) {
      print('✓ FCM Token ready: ${token.substring(0, 30)}...');
      print('   ⚠ Belum disimpan ke server (menunggu login)');
    } else {
      print('⚠ FCM Token belum tersedia');
    }

    // 5. Listen to token refresh — cek auth state sebelum save
    print('5️⃣  Setting up token refresh listener...');
    _messaging.onTokenRefresh.listen((String newToken) {
      print('🔄 FCM Token refreshed: ${newToken.substring(0, 30)}...');
      // Hanya save jika user sudah login (ada auth token)
      if (ApiService.token != null) {
        print('   → User sudah login, saving new FCM token...');
        ApiService.saveFcmToken(newToken);
      } else {
        print('   ⚠ User belum login, token refresh disimpan nanti saat login');
      }
    });
    print('✓ Token refresh listener active');

    // 6. Handle foreground messages
    print('6️⃣  Setting up foreground message handler...');
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      print('\n🔔 [FOREGROUND] Message received: ${message.messageId}');
      final RemoteNotification? notif = message.notification;
      final Map<String, dynamic> data = message.data;
      
      print('   Title: ${notif?.title}');
      print('   Body: ${notif?.body}');
      print('   Data: $data');
      
      if (notif != null) {
        flutterLocalNotificationsPlugin.show(
          notif.hashCode,
          notif.title,
          notif.body,
          NotificationDetails(
            android: AndroidNotificationDetails(
              _channelId,
              _channelName,
              channelDescription: _channelDesc,
              importance: Importance.high,
              priority: Priority.high,
              icon: '@mipmap/ic_launcher',
              enableVibration: true,
              enableLights: true,
              playSound: true,
            ),
          ),
          payload: data.toString(),
        );
        print('   ✓ Foreground notification displayed');
      }
    });
    print('✓ Foreground handler active\n');

    print('=== NOTIFICATION SERVICE INITIALIZED ===\n');
  }

  /// Dipanggil SETELAH login berhasil untuk menyimpan FCM token ke server.
  /// Ini memastikan auth token sudah tersedia saat save FCM token.
  static Future<void> saveTokenAfterLogin() async {
    print('\n=== SAVING FCM TOKEN AFTER LOGIN ===');
    try {
      final String? fcmToken = await _messaging.getToken();
      if (fcmToken != null) {
        print('✓ FCM Token: ${fcmToken.substring(0, 30)}...');
        await ApiService.saveFcmToken(fcmToken);
        print('✓ FCM token saved to server after login');
      } else {
        print('⚠ Gagal dapat FCM token dari Firebase');
      }
    } catch (e) {
      print('❌ Error saving FCM token after login: $e');
    }
  }
}