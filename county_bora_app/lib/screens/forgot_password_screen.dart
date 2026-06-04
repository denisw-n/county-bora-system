import 'package:flutter/material.dart';
import '../services/api_service.dart'; // Import your ApiService

class ForgotPasswordScreen extends StatefulWidget {
  const ForgotPasswordScreen({super.key});

  @override
  State<ForgotPasswordScreen> createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends State<ForgotPasswordScreen> {
  final TextEditingController _emailController = TextEditingController();
  final ApiService _apiService = ApiService(); // Use the service
  bool _isLoading = false;

  Future<void> _sendResetLink() async {
    if (_emailController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Please enter your email")));
      return;
    }

    setState(() => _isLoading = true);

    try {
      // Use the ApiService helper we are about to add
      final result = await _apiService.forgotPassword(_emailController.text.trim());

      if (mounted) {
        setState(() => _isLoading = false);
        // Assuming your server returns a 'message' key on success
        if (result.containsKey('message')) {
          ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(result['message'])));
          Navigator.pop(context);
        } else {
          ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(result['error'] ?? "Failed to send link.")));
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Network error. Please try again.")));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Reset Password")),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            TextField(
              controller: _emailController,
              decoration: const InputDecoration(labelText: "Email Address"),
              keyboardType: TextInputType.emailAddress,
            ),
            const SizedBox(height: 20),
            _isLoading
                ? const CircularProgressIndicator()
                : ElevatedButton(
                onPressed: _sendResetLink,
                child: const Text("Send Reset Link")
            ),
          ],
        ),
      ),
    );
  }
}