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
      final type = message.data['type']?.toString() ?? '';
      if (message.notification != null || type == 'provider_dispatch_offer' || type == 'provider_order') {
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
      final type = message.data['type']?.toString() ?? '';
      final incoming = type == 'provider_dispatch_offer' || type == 'provider_order';
      final channel = AndroidNotificationChannel(
        incoming ? 'provider_dispatch_offer' : 'arrow-provider',
        incoming ? 'Pedidos próximos' : 'Arrow Prestador',
        description: incoming ? 'Chamado de prestador próximo' : 'Notificações do prestador',
        importance: Importance.max,
        playSound: true,
      );
      await flutterLocalNotificationsPlugin
          .resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>()
          ?.createNotificationChannel(channel);
      final androidDetails = AndroidNotificationDetails(
        channel.id,
        channel.name,
        channelDescription: channel.description,
        importance: Importance.max,
        priority: Priority.max,
        playSound: true,
        category: AndroidNotificationCategory.call,
        fullScreenIntent: incoming,
      );
      const iosDetails = DarwinNotificationDetails(presentAlert: true, presentBadge: true, presentSound: true);
      await FlutterLocalNotificationsPlugin().show(
        id: 0,
        title: message.notification?.title ?? (incoming ? 'Novo pedido próximo' : 'Arrow Prestador'),
        body: message.notification?.body ?? (incoming ? 'Um cliente pediu um prestador próximo.' : ''),
        notificationDetails: NotificationDetails(android: androidDetails, iOS: iosDetails),
        payload: jsonEncode(message.data),
      );
    } on Exception catch (e) {
      log(e.toString());
    }
  }
}
