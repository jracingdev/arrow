import 'dart:convert';
import 'dart:developer';

import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';

@pragma('vm:entry-point')
Future<void> firebaseMessageBackgroundHandle(RemoteMessage message) async {
  log("BackGround Message :: ${message.messageId}");
}

class NotificationService {
  FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin = FlutterLocalNotificationsPlugin();

  Future<void> initInfo() async {
    await FirebaseMessaging.instance.setForegroundNotificationPresentationOptions(alert: true, badge: true, sound: true);
    final request = await FirebaseMessaging.instance.requestPermission(
      alert: true,
      announcement: false,
      badge: true,
      carPlay: false,
      criticalAlert: false,
      provisional: false,
      sound: true,
    );

    if (request.authorizationStatus == AuthorizationStatus.authorized || request.authorizationStatus == AuthorizationStatus.provisional) {
      const androidInit = AndroidInitializationSettings('@mipmap/ic_launcher');
      const iosInit = DarwinInitializationSettings();
      const settings = InitializationSettings(android: androidInit, iOS: iosInit);
      await flutterLocalNotificationsPlugin.initialize(settings: settings);
      setupInteractedMessage();
    }
  }

  Future<void> setupInteractedMessage() async {
    FirebaseMessaging.onBackgroundMessage(firebaseMessageBackgroundHandle);
    FirebaseMessaging.onMessage.listen((RemoteMessage message) async {
      if (message.notification != null) {
        display(message);
      }
    });
    try {
      await FirebaseMessaging.instance.subscribeToTopic('provider');
    } catch (e) {
      log('FCM subscribe provider: $e');
    }
  }

  static Future<String> getToken() async {
    try {
      return await FirebaseMessaging.instance.getToken() ?? '';
    } catch (e) {
      log('FCM getToken: $e');
      return '';
    }
  }

  void display(RemoteMessage message) async {
    try {
      const channel = AndroidNotificationChannel(
        'arrow-provider',
        'Arrow Prestador',
        description: 'Notificações do prestador',
        importance: Importance.max,
      );
      final androidDetails = AndroidNotificationDetails(
        channel.id,
        channel.name,
        channelDescription: channel.description,
        importance: Importance.high,
        priority: Priority.high,
      );
      const iosDetails = DarwinNotificationDetails(presentAlert: true, presentBadge: true, presentSound: true);
      await FlutterLocalNotificationsPlugin().show(
        id: 0,
        title: message.notification?.title,
        body: message.notification?.body,
        notificationDetails: NotificationDetails(android: androidDetails, iOS: iosDetails),
        payload: jsonEncode(message.data),
      );
    } on Exception catch (e) {
      log(e.toString());
    }
  }
}
