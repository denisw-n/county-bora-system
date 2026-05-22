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

  // Verify both exist for a valid session
  bool hasValidSession = token != null && userJson != null;
  Map<String, dynamic>? userData;

  if (hasValidSession) {
    try {
      userData = jsonDecode(userJson!);
    } catch (e) {
      await prefs.clear();
      hasValidSession = false; // Reset if data is corrupt
    }
  }

  runApp(CountyBoraApp(
      initialUserData: userData,
      isLoggedIn: hasValidSession
  ));
}

class CountyBoraApp extends StatelessWidget {
  final Map<String, dynamic>? initialUserData;
  final bool isLoggedIn;

  const CountyBoraApp({super.key, this.initialUserData, required this.isLoggedIn});

  @override
  Widget build(BuildContext context) {
    const Color countyGreen = Color(0xFF008444);

    return MaterialApp(
      title: 'County Bora',
      navigatorKey: navigatorKey, // Navigation context is now global
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: countyGreen,
          primary: countyGreen,
        ),
        useMaterial3: true,
      ),

      // Use the boolean flag to decide start point
      initialRoute: isLoggedIn ? '/dashboard' : '/login',

      routes: {
        '/login': (context) => const LoginScreen(),
      },

      onGenerateRoute: (settings) {
        if (settings.name == '/dashboard') {
          final args = settings.arguments as Map<dynamic, dynamic>?;
          // Ensure we have data; fallback to initialUserData if route args are empty
          final activeData = args != null
              ? Map<String, dynamic>.from(args)
              : (initialUserData ?? {});

          return MaterialPageRoute(
            builder: (context) => DashboardScreen(userData: activeData),
          );
        }
        return MaterialPageRoute(builder: (context) => const LoginScreen());
      },
    );
  }
}