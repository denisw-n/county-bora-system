import 'package:flutter/material.dart';
import 'screens/login_screen.dart';
import 'screens/dashboard_screen.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';

// Global key used for navigation outside of BuildContexts
final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  final prefs = await SharedPreferences.getInstance();
  final String? token = prefs.getString('auth_token');
  final String? userJson = prefs.getString('user_data');

  Map<String, dynamic>? userData;

  if (token != null && userJson != null) {
    try {
      userData = jsonDecode(userJson);
    } catch (e) {
      await prefs.clear();
    }
  }

  runApp(CountyBoraApp(initialUserData: userData));
}

class CountyBoraApp extends StatelessWidget {
  final Map<String, dynamic>? initialUserData;

  const CountyBoraApp({super.key, this.initialUserData});

  @override
  Widget build(BuildContext context) {
    const Color countyGreen = Color(0xFF008444);

    return MaterialApp(
      title: 'County Bora',
      navigatorKey: navigatorKey,
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: countyGreen,
          primary: countyGreen,
        ),
        useMaterial3: true,
      ),
      // Define initial route
      initialRoute: initialUserData != null ? '/dashboard' : '/login',

      routes: {
        '/login': (context) => const LoginScreen(),
      },

      onGenerateRoute: (settings) {
        if (settings.name == '/dashboard') {
          final args = settings.arguments as Map<dynamic, dynamic>?;
          final activeData = args ?? initialUserData ?? {};

          return MaterialPageRoute(
            builder: (context) => DashboardScreen(userData: activeData),
          );
        }

        // Fallback safety route
        return MaterialPageRoute(builder: (context) => const LoginScreen());
      },
    );
  }
}