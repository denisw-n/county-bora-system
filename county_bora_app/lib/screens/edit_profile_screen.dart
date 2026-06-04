import 'package:flutter/material.dart';
import '../services/api_service.dart';

class EditProfileScreen extends StatefulWidget {
  final Map<String, dynamic> currentData;
  const EditProfileScreen({super.key, required this.currentData});

  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  final ApiService _apiService = ApiService();
  final _formKey = GlobalKey<FormState>();
  final Color _countyGreen = const Color(0xFF008444);

  late TextEditingController _phoneController;
  String? _selectedWardId;
  List<dynamic> _wards = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    // Pre-fill with existing phone number
    _phoneController = TextEditingController(text: widget.currentData['phone'] ?? '');
    _fetchWards();
  }

  @override
  void dispose() {
    _phoneController.dispose();
    super.dispose();
  }

  Future<void> _fetchWards() async {
    final wards = await _apiService.getWards();
    setState(() {
      _wards = wards;
      _isLoading = false;
    });
  }

  Future<void> _submitUpdate() async {
    if (!_formKey.currentState!.validate()) return;

    // Show loading indicator
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text("Updating profile...")),
    );

    final result = await _apiService.updateProfile(
      phoneNumber: _phoneController.text,
      wardId: _selectedWardId,
    );

    if (mounted) {
      if (result.containsKey('status') && result['status'] == 'success') {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Profile updated successfully!")),
        );
        // Pop back to the profile screen and send "true" to trigger a refresh
        Navigator.pop(context, true);
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(result['message'] ?? "Update failed")),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Edit Profile", style: TextStyle(color: Colors.white)),
        backgroundColor: _countyGreen,
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator(color: _countyGreen))
          : Form(
        key: _formKey,
        child: ListView(
          padding: const EdgeInsets.all(20),
          children: [
            TextFormField(
              controller: _phoneController,
              keyboardType: TextInputType.phone,
              decoration: const InputDecoration(
                labelText: "Phone Number",
                border: OutlineInputBorder(),
                prefixIcon: Icon(Icons.phone),
              ),
              validator: (v) => (v == null || v.isEmpty) ? "Phone number is required" : null,
            ),
            const SizedBox(height: 20),
            DropdownButtonFormField<String>(
              decoration: const InputDecoration(
                labelText: "Change Ward",
                border: OutlineInputBorder(),
                prefixIcon: Icon(Icons.location_on),
              ),
              hint: const Text("Select a new ward"),
              items: _wards.map((w) => DropdownMenuItem<String>(
                value: w['id'].toString(),
                child: Text(w['name']),
              )).toList(),
              onChanged: (val) => setState(() => _selectedWardId = val),
              validator: (v) => (v == null && _selectedWardId == null) ? "Please select a ward" : null,
            ),
            const SizedBox(height: 30),
            SizedBox(
              height: 50,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: _countyGreen,
                  foregroundColor: Colors.white,
                ),
                onPressed: _submitUpdate,
                child: const Text("SAVE CHANGES", style: TextStyle(fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}