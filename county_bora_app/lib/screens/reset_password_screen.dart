import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

import '../config/api_constants.dart';

class ResetPasswordScreen extends StatefulWidget {
  const ResetPasswordScreen({super.key});

  @override
  State<ResetPasswordScreen> createState() => _ResetPasswordScreenState();
}

class _ResetPasswordScreenState extends State<ResetPasswordScreen> {
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  bool _isLoading = false;

  Future<void> _resetPassword(String email, String token) async {
    setState(() => _isLoading = true);

    // Replace with your actual local IP address if testing on a physical device
    final response = await http.post(
      Uri.parse('http://${ApiConstants.baseUrl}:8000/api/reset-password'),
      body: {
        'email': email,
        'token': token,
        'password': _passwordController.text,
        'password_confirmation': _confirmPasswordController.text,
      },
    );

    setState(() => _isLoading = false);

    if (response.statusCode == 200) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Password reset successful!')));
      Navigator.pushNamedAndRemoveUntil(context, '/login', (route) => false);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Reset failed. Token might be expired.')));
    }
  }

  @override
  Widget build(BuildContext context) {
    // Get arguments passed from the deep link
    final args = ModalRoute.of(context)!.settings.arguments as Map<String, dynamic>;
    final token = args['token'];
    final email = args['email'];

    return Scaffold(
      appBar: AppBar(title: const Text("Reset Password")),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            TextField(controller: _passwordController, decoration: const InputDecoration(labelText: "New Password"), obscureText: true),
            TextField(controller: _confirmPasswordController, decoration: const InputDecoration(labelText: "Confirm Password"), obscureText: true),
            const SizedBox(height: 20),
            _isLoading
                ? const CircularProgressIndicator()
                : ElevatedButton(onPressed: () => _resetPassword(email, token), child: const Text("Update Password")),
          ],
        ),
      ),
    );
  }
}