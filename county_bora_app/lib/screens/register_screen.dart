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
  final _apiService = ApiService();

  // Controllers
  final _firstName = TextEditingController();
  final _middleName = TextEditingController(); // Added Middle Name Controller
  final _lastName = TextEditingController();
  final _email = TextEditingController();
  final _nationalId = TextEditingController();
  final _phone = TextEditingController();
  final _password = TextEditingController();
  final _confirmPassword = TextEditingController();

  // State variables
  File? _idImage;
  String? _selectedWardId;
  List<dynamic> _wards = [];
  bool _isLoading = false;
  bool _isPasswordVisible = false;

  @override
  void initState() {
    super.initState();
    _loadWards();
  }

  @override
  void dispose() {
    _firstName.dispose();
    _middleName.dispose(); // Added Middle Name Clean up
    _lastName.dispose();
    _email.dispose();
    _nationalId.dispose();
    _phone.dispose();
    _password.dispose();
    _confirmPassword.dispose();
    super.dispose();
  }

  void _loadWards() async {
    try {
      final dynamic response = await _apiService.getWards();

      if (response is Map) {
        final mapResponse = Map<String, dynamic>.from(response);
        if (mapResponse.containsKey('data') && mapResponse['data'] is List) {
          setState(() {
            _wards = mapResponse['data'];
          });
        }
      } else if (response is List) {
        setState(() {
          _wards = response;
        });
      }
    } catch (e) {
      debugPrint("Ward Load Error: $e");
      _showMsg("Failed to load wards. Check connection.", true);
    }
  }

  Future<void> _pickImage() async {
    final pickedFile = await ImagePicker().pickImage(
      source: ImageSource.gallery,
      imageQuality: 80,
    );
    if (pickedFile != null) {
      setState(() => _idImage = File(pickedFile.path));
    }
  }

  void _handleRegister() async {
    if (_firstName.text.isEmpty || _lastName.text.isEmpty || _email.text.isEmpty) {
      _showMsg("Basic details are required", true);
      return;
    }
    if (_selectedWardId == null || _idImage == null) {
      _showMsg("Ward and ID photo are mandatory for verification", true);
      return;
    }
    if (_password.text != _confirmPassword.text) {
      _showMsg("Passwords do not match!", true);
      return;
    }

    setState(() => _isLoading = true);

    try {
      final res = await _apiService.registerUser(
        firstName: _firstName.text.trim(),
        middleName: _middleName.text.trim(), // ✅ FIXED: Now actively passing middleName string
        lastName: _lastName.text.trim(),
        email: _email.text.trim(),
        password: _password.text.trim(),
        confirmPassword: _confirmPassword.text.trim(),
        nationalId: _nationalId.text.trim(),
        phoneNumber: _phone.text.trim(),
        wardId: _selectedWardId!,
        idImage: _idImage!,
      );

      // ✅ UPDATED: Checking for typical successful registration payload structures
      if (res.containsKey('user') || res.containsKey('access_token') || res['message']?.toString().contains('successful') == true) {
        _showMsg(res['message'] ?? "Registration successful! Approval pending.", false);
        if (mounted) Navigator.pop(context);
      } else {
        String errorMsg = "Registration failed";

        // ✅ FIXED: Deep parse validation array mapping schemas sent by $validator->errors() directly
        if (res['errors'] != null) {
          var firstError = (res['errors'] as Map).values.first;
          errorMsg = firstError is List ? firstError[0] : firstError.toString();
        } else if (res['message'] != null) {
          errorMsg = res['message'];
        } else if (res.isNotEmpty) {
          // Captures raw flattened validation map dumps e.g. {"national_id":["..."]}
          var firstError = res.values.first;
          errorMsg = firstError is List ? firstError[0] : firstError.toString();
        }

        debugPrint("🛑 SERVER REJECTION PAYLOAD DATA: $res");
        _showMsg(errorMsg, true);
      }
    } catch (e) {
      debugPrint("🛑 FLUTTER SUBMIT ROUTE ERROR: $e");
      _showMsg("A network error occurred. Please try again.", true);
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _showMsg(String m, bool isError) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(m),
        backgroundColor: isError ? Colors.redAccent : Colors.green,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    const primaryGreen = Color(0xFF008444);
    final inputBorderColor = primaryGreen.withOpacity(0.3);

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.white,
        iconTheme: const IconThemeData(color: primaryGreen),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              "Citizen Registration",
              style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.black87),
            ),
            const SizedBox(height: 5),
            const Text(
              "Join County Bora to report and track issues.",
              style: TextStyle(color: Colors.grey, fontSize: 14),
            ),
            const SizedBox(height: 25),

            // Name Block Row with First, Middle, and Last name aligned side-by-side
            Row(children: [
              Expanded(child: _buildInput(_firstName, "FIRST NAME", inputBorderColor)),
              const SizedBox(width: 10),
              Expanded(child: _buildInput(_middleName, "MIDDLE NAME", inputBorderColor)),
              const SizedBox(width: 10),
              Expanded(child: _buildInput(_lastName, "LAST NAME", inputBorderColor)),
            ]),

            _buildInput(_email, "EMAIL ADDRESS", inputBorderColor, keyboard: TextInputType.emailAddress),
            _buildInput(_nationalId, "NATIONAL ID NUMBER", inputBorderColor, keyboard: TextInputType.number),
            _buildInput(_phone, "PHONE NUMBER", inputBorderColor, keyboard: TextInputType.phone),

            _buildInput(
              _password, "PASSWORD", inputBorderColor,
              isPass: !_isPasswordVisible,
              suffix: IconButton(
                icon: Icon(_isPasswordVisible ? Icons.visibility : Icons.visibility_off, size: 18),
                onPressed: () => setState(() => _isPasswordVisible = !_isPasswordVisible),
              ),
            ),

            _buildInput(_confirmPassword, "CONFIRM PASSWORD", inputBorderColor, isPass: true),

            const Text("SELECT WARD", style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: primaryGreen)),
            const SizedBox(height: 8),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12),
              decoration: BoxDecoration(
                color: Colors.grey[50],
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: inputBorderColor, width: 1.5),
              ),
              child: DropdownButtonHideUnderline(
                child: DropdownButton<String>(
                  value: _selectedWardId,
                  hint: const Text("Choose your local ward"),
                  isExpanded: true,
                  items: _wards.map((w) => DropdownMenuItem<String>(
                    value: w['id'].toString(),
                    child: Text(w['name'].toString()),
                  )).toList(),
                  onChanged: (v) => setState(() => _selectedWardId = v),
                ),
              ),
            ),

            const SizedBox(height: 25),
            const Text("NATIONAL ID PHOTO", style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: primaryGreen)),
            const SizedBox(height: 8),
            GestureDetector(
              onTap: _pickImage,
              child: Container(
                height: 150,
                width: double.infinity,
                decoration: BoxDecoration(
                  color: Colors.grey[50],
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: inputBorderColor, width: 1.5),
                ),
                child: _idImage == null
                    ? Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: const [
                    Icon(Icons.add_a_photo, color: primaryGreen, size: 40),
                    SizedBox(height: 10),
                    Text("Upload clear photo of ID", style: TextStyle(color: Colors.grey, fontSize: 12)),
                  ],
                )
                    : ClipRRect(
                  borderRadius: BorderRadius.circular(10),
                  child: Image.file(_idImage!, fit: BoxFit.cover),
                ),
              ),
            ),

            const SizedBox(height: 40),

            SizedBox(
              width: double.infinity,
              height: 55,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _handleRegister,
                style: ElevatedButton.styleFrom(
                  backgroundColor: primaryGreen,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                  elevation: 2,
                ),
                child: _isLoading
                    ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                    : const Text("CREATE ACCOUNT", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
              ),
            ),
            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }

  Widget _buildInput(
      TextEditingController c,
      String l,
      Color b,
      {bool isPass = false, TextInputType keyboard = TextInputType.text, Widget? suffix}
      ) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(l, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF008444))),
          const SizedBox(height: 8),
          TextField(
            controller: c,
            obscureText: isPass,
            keyboardType: keyboard,
            decoration: InputDecoration(
              suffixIcon: suffix,
              filled: true,
              fillColor: Colors.grey[50],
              contentPadding: const EdgeInsets.symmetric(horizontal: 15, vertical: 15),
              enabledBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(10),
                borderSide: BorderSide(color: b, width: 1.5),
              ),
              focusedBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(10),
                borderSide: const BorderSide(color: Color(0xFF008444), width: 2),
              ),
            ),
          ),
        ],
      ),
    );
  }
}