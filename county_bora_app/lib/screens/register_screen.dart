import 'package:flutter/material.dart';
import 'dart:io';
import 'package:image_picker/image_picker.dart';
import '../services/api_service.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _firstName = TextEditingController();
  final _lastName = TextEditingController();
  final _email = TextEditingController();
  final _nationalId = TextEditingController();
  final _phone = TextEditingController();
  final _password = TextEditingController();
  String? _selectedWardId = "ward-uuid-here"; // This should come from an API later
  File? _idImage;

  Future<void> _pickImage() async {
    final pickedFile = await ImagePicker().pickImage(source: ImageSource.gallery);
    if (pickedFile != null) setState(() => _idImage = File(pickedFile.path));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Create Account"), foregroundColor: Colors.green),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text("Join the Portal", style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold)),
            const Text("Register to start reporting issues in your area."),
            const SizedBox(height: 30),

            Row(
              children: [
                Expanded(child: _buildField(_firstName, "FIRST NAME")),
                const SizedBox(width: 10),
                Expanded(child: _buildField(_lastName, "LAST NAME")),
              ],
            ),
            _buildField(_email, "EMAIL"),
            _buildField(_nationalId, "NATIONAL ID"),
            _buildField(_phone, "PHONE NUMBER"),
            _buildField(_password, "PASSWORD", isPass: true),

            const SizedBox(height: 20),
            const Text("UPLOAD NATIONAL ID PHOTO", style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 10),
            GestureDetector(
              onTap: _pickImage,
              child: Container(
                height: 100,
                width: double.infinity,
                decoration: BoxDecoration(color: Colors.grey[200], borderRadius: BorderRadius.circular(10), borderSide: const BorderSide(color: Colors.grey, style: BorderStyle.none)),
                child: _idImage == null
                    ? const Column(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.camera_alt, color: Colors.grey), Text("Tap to upload ID")])
                    : Image.file(_idImage!, fit: BoxFit.cover),
              ),
            ),

            const SizedBox(height: 30),
            SizedBox(
              width: double.infinity,
              height: 55,
              child: ElevatedButton(
                onPressed: () {}, // Add registration logic here
                style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF008444), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))),
                child: const Text("REGISTER NOW →", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildField(TextEditingController controller, String label, {bool isPass = false}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 15),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.grey)),
          const SizedBox(height: 5),
          TextField(
            controller: controller,
            obscureText: isPass,
            decoration: InputDecoration(filled: true, fillColor: Colors.grey[100], border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide.none)),
          ),
        ],
      ),
    );
  }
}