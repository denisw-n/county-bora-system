import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../services/api_service.dart';
import 'package:county_bora_app/widgets/app_refresh_indicator.dart';

class HotlinesScreen extends StatefulWidget {
  const HotlinesScreen({super.key});

  @override
  State<HotlinesScreen> createState() => _HotlinesScreenState();
}

class _HotlinesScreenState extends State<HotlinesScreen> {
  final ApiService _apiService = ApiService();

  List<dynamic> _hotlines = [];
  bool _isLoading = true;

  final Color _countyGreen = const Color(0xFF008444);
  final Color _bgColor = const Color(0xFFE8EFE8);
  final Color _accentYellow = const Color(0xFFFFD700);

  @override
  void initState() {
    super.initState();
    _loadHotlines();
  }

  Future<void> _loadHotlines() async {
    setState(() => _isLoading = true);
    final data = await _apiService.getHotlines();
    if (mounted) {
      setState(() {
        _hotlines = data;
        _isLoading = false;
      });
    }
  }

  Future<void> _makeCall(String phoneNumber) async {
    final Uri launchUri = Uri(scheme: 'tel', path: phoneNumber);
    if (await canLaunchUrl(launchUri)) {
      await launchUrl(launchUri);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: _bgColor,
      appBar: AppBar(
        title: const Text("Emergency Support", style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: _countyGreen,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator(color: _countyGreen))
          : AppRefreshIndicator(
        onRefresh: _loadHotlines,
        child: ListView(
          physics: const AlwaysScrollableScrollPhysics(),
          children: [
            // Header Block
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 25),
              decoration: BoxDecoration(
                color: _countyGreen,
                borderRadius: const BorderRadius.only(
                  bottomLeft: Radius.circular(30),
                  bottomRight: Radius.circular(30),
                ),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Icon(Icons.shield_outlined, color: Colors.white, size: 40),
                  const SizedBox(height: 15),
                  const Text(
                    "Help is Available",
                    style: TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 5),
                  Text(
                    "We are here to help. Tap any service below to speak with an emergency responder immediately.",
                    style: TextStyle(color: Colors.white.withOpacity(0.9), fontSize: 14),
                  ),
                ],
              ),
            ),

            // Hotlines List
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
              child: _hotlines.isEmpty
                  ? const Center(child: Text("No hotlines available."))
                  : Column(
                children: _hotlines.map((item) => Container(
                  margin: const EdgeInsets.only(bottom: 15),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: _countyGreen.withOpacity(0.2)),
                    boxShadow: [
                      BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 8, offset: const Offset(0, 4))
                    ],
                  ),
                  child: ListTile(
                    contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                    leading: Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(color: _accentYellow.withOpacity(0.2), borderRadius: BorderRadius.circular(12)),
                      child: Icon(Icons.emergency, color: _countyGreen),
                    ),
                    title: Text(item['service_name'] ?? 'Service', style: const TextStyle(fontWeight: FontWeight.bold)),
                    subtitle: Text(item['phone_number'] ?? '', style: TextStyle(color: Colors.grey[600])),
                    trailing: Icon(Icons.call, color: _countyGreen),
                    onTap: () => _makeCall(item['phone_number']),
                  ),
                )).toList(),
              ),
            ),
          ],
        ),
      ),
    );
  }
}