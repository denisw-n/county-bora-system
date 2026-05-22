import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';

import '../services/api_service.dart';
import 'register_screen.dart';
import '../main.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _apiService = ApiService();

  final _loginController = TextEditingController();
  final _passwordController = TextEditingController();

  bool _isLoading = false;

  final Color _countyGreen = const Color(0xFF008444);

  Future<void> _handleLogin() async {
    if (!_formKey.currentState!.validate()) return;

    FocusScope.of(context).unfocus();

    setState(() {
      _isLoading = true;
    });

    try {
      final res = await _apiService.loginUser(
        login: _loginController.text.trim(),
        password: _passwordController.text.trim(),
      );

      print("LOGIN RESPONSE: $res");

      if (res.containsKey('error')) {
        setState(() {
          _isLoading = false;
        });

        _showMsg(res['error'], true);
        return;
      }

      if (!res.containsKey('access_token')) {
        setState(() {
          _isLoading = false;
        });

        _showMsg("Authentication token missing.", true);
        return;
      }

      final prefs = await SharedPreferences.getInstance();

      await prefs.setString(
        'auth_token',
        res['access_token'],
      );

      await prefs.setString(
        'user_data',
        jsonEncode(res['user']),
      );

      print("TOKEN SAVED");
      print("USER SAVED");

      if (!mounted) return;

      setState(() {
        _isLoading = false;
      });

      navigatorKey.currentState?.pushNamedAndRemoveUntil(
        '/dashboard',
            (route) => false,
        arguments: res,
      );

    } catch (e) {
      print("LOGIN ERROR: $e");

      if (!mounted) return;

      setState(() {
        _isLoading = false;
      });

      _showMsg(
        "Network error. Check server connection.",
        true,
      );
    }
  }

  void _showMsg(String msg, bool isError) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(msg),
        backgroundColor:
        isError ? Colors.red : _countyGreen,
      ),
    );
  }

  InputDecoration _inputDecoration(
      String label,
      IconData icon,
      ) {
    return InputDecoration(
      labelText: label,
      prefixIcon: Icon(
        icon,
        color: _countyGreen,
      ),
      filled: true,
      fillColor: Colors.grey[100],
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: BorderSide.none,
      ),
    );
  }

  Widget _buildRegisterLink() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        const Text("Don't have an account? "),
        GestureDetector(
          onTap: () {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (c) =>
                const RegisterScreen(),
              ),
            );
          },
          child: Text(
            "Register here",
            style: TextStyle(
              color: _countyGreen,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,

      body: Center(
        child: SingleChildScrollView(
          child: Padding(
            padding:
            const EdgeInsets.symmetric(horizontal: 30),

            child: Form(
              key: _formKey,

              child: Column(
                mainAxisAlignment:
                MainAxisAlignment.center,

                children: [
                  const SizedBox(height: 20),

                  CircleAvatar(
                    radius: 60,
                    backgroundColor: Colors.transparent,

                    child: Image.asset(
                      'assets/images/logo.png',

                      errorBuilder: (c, e, s) {
                        return Icon(
                          Icons.location_city,
                          size: 60,
                          color: _countyGreen,
                        );
                      },
                    ),
                  ),

                  const SizedBox(height: 20),

                  Text(
                    "Nairobi County\nService Portal",

                    textAlign: TextAlign.center,

                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: _countyGreen,
                    ),
                  ),

                  const SizedBox(height: 40),

                  TextFormField(
                    controller: _loginController,

                    decoration: _inputDecoration(
                      "Email or National ID",
                      Icons.person_outline,
                    ),

                    validator: (v) {
                      if (v == null || v.isEmpty) {
                        return "Required field";
                      }

                      return null;
                    },
                  ),

                  const SizedBox(height: 20),

                  TextFormField(
                    controller: _passwordController,
                    obscureText: true,

                    decoration: _inputDecoration(
                      "Password",
                      Icons.lock_outline,
                    ),

                    validator: (v) {
                      if (v == null || v.isEmpty) {
                        return "Password required";
                      }

                      return null;
                    },
                  ),

                  const SizedBox(height: 30),

                  SizedBox(
                    width: double.infinity,
                    height: 55,

                    child: ElevatedButton(
                      onPressed:
                      _isLoading ? null : _handleLogin,

                      style:
                      ElevatedButton.styleFrom(
                        backgroundColor:
                        _countyGreen,
                      ),

                      child: _isLoading
                          ? const CircularProgressIndicator(
                        color: Colors.white,
                      )
                          : const Text(
                        "LOGIN",

                        style: TextStyle(
                          color: Colors.white,
                          fontWeight:
                          FontWeight.bold,
                        ),
                      ),
                    ),
                  ),

                  const SizedBox(height: 25),

                  _buildRegisterLink(),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}