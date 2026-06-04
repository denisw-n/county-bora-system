import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart'; // Added for logout logic
import '../services/api_service.dart';
import 'edit_profile_screen.dart';
import '../main.dart'; // Needed to access navigatorKey

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final ApiService _apiService = ApiService();

  // Theme Colors
  final Color _countyGreen = const Color(0xFF008444);
  final Color _bgSoft = const Color(0xFFF4F7F5);
  final Color _cardColor = Colors.white;

  Map<String, dynamic>? _userProfile;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadProfile();
  }

  Future<void> _loadProfile() async {
    final profile = await _apiService.getProfile();
    if (mounted) {
      setState(() {
        _userProfile = profile;
        _isLoading = false;
      });
    }
  }

  // Logout Logic
  Future<void> _handleLogout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    if (mounted) {
      navigatorKey.currentState?.pushNamedAndRemoveUntil('/login', (route) => false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: _bgSoft,
      appBar: AppBar(
        title: const Text("My Profile", style: TextStyle(color: Colors.white, fontWeight: FontWeight.w600)),
        backgroundColor: _countyGreen,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator(color: _countyGreen))
          : _userProfile!.containsKey('error')
          ? Center(child: Text("Error: ${_userProfile!['error']}"))
          : SingleChildScrollView(
        child: Column(
          children: [
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 30, horizontal: 20),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [_countyGreen, _countyGreen.withOpacity(0.8)],
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                ),
                borderRadius: const BorderRadius.only(
                  bottomLeft: Radius.circular(30),
                  bottomRight: Radius.circular(30),
                ),
              ),
              child: Column(
                children: [
                  const CircleAvatar(radius: 50, backgroundColor: Colors.white24, child: Icon(Icons.person, size: 60, color: Colors.white)),
                  const SizedBox(height: 15),
                  Text(_userProfile!['full_name'], style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: Colors.white)),
                  Text("ID: ${_userProfile!['national_id']}", style: const TextStyle(color: Colors.white70, fontSize: 14)),
                ],
              ),
            ),

            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                children: [
                  _buildDetailBox(Icons.phone, "PHONE NUMBER", _userProfile!['phone']),
                  _buildDetailBox(Icons.email, "EMAIL", _userProfile!['email']),
                  _buildDetailBox(Icons.location_on, "PRIMARY WARD", "${_userProfile!['ward']}, ${_userProfile!['sub_county']}"),

                  Container(
                    margin: const EdgeInsets.only(top: 10),
                    padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15),
                    decoration: BoxDecoration(color: _cardColor, borderRadius: BorderRadius.circular(12), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 5))]),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text("Verification Status", style: TextStyle(fontWeight: FontWeight.w600, color: Colors.black87)),
                        Container(padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6), decoration: BoxDecoration(color: Colors.green.withOpacity(0.1), borderRadius: BorderRadius.circular(20)), child: const Text("VERIFIED", style: TextStyle(color: Color(0xFF2E7D32), fontSize: 12, fontWeight: FontWeight.bold))),
                      ],
                    ),
                  ),

                  const SizedBox(height: 30),

                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(backgroundColor: _countyGreen, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), padding: const EdgeInsets.symmetric(vertical: 16)),
                      onPressed: () async {
                        final result = await Navigator.push(context, MaterialPageRoute(builder: (context) => EditProfileScreen(currentData: _userProfile!)));
                        if (result == true) _loadProfile();
                      },
                      child: const Text("EDIT PROFILE", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, letterSpacing: 1)),
                    ),
                  ),

                  // Professional Log Out Button
                  const SizedBox(height: 15),
                  TextButton.icon(
                    onPressed: _handleLogout,
                    icon: const Icon(Icons.exit_to_app, color: Colors.redAccent),
                    label: const Text("Log Out", style: TextStyle(color: Colors.redAccent, fontWeight: FontWeight.bold)),
                  ),
                  const SizedBox(height: 20),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDetailBox(IconData icon, String label, String value) {
    return Container(
      margin: const EdgeInsets.symmetric(vertical: 8),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: _cardColor, borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.black.withOpacity(0.05))),
      child: Row(
        children: [
          Container(padding: const EdgeInsets.all(8), decoration: BoxDecoration(color: _bgSoft, borderRadius: BorderRadius.circular(8)), child: Icon(icon, color: _countyGreen, size: 20)),
          const SizedBox(width: 15),
          Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(label, style: const TextStyle(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.bold, letterSpacing: 0.5)),
            const SizedBox(height: 2),
            Text(value, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600, color: Colors.black87)),
          ]),
        ],
      ),
    );
  }
}