import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../services/api_service.dart';

class HotlinesScreen extends StatefulWidget {
  const HotlinesScreen({super.key});

  @override
  State<HotlinesScreen> createState() => _HotlinesScreenState();
}

class _HotlinesScreenState extends State<HotlinesScreen> {
  final ApiService _apiService = ApiService();
  late Future<List<dynamic>> _hotlinesFuture;

  @override
  void initState() {
    super.initState();
    _hotlinesFuture = _apiService.getHotlines();
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
      appBar: AppBar(
        title: const Text("Emergency Hotlines"),
        backgroundColor: const Color(0xFF008444),
        foregroundColor: Colors.white,
      ),
      body: FutureBuilder<List<dynamic>>(
        future: _hotlinesFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }
          if (snapshot.hasError || !snapshot.hasData || snapshot.data!.isEmpty) {
            return const Center(child: Text("No hotlines available."));
          }

          return ListView.separated(
            padding: const EdgeInsets.all(15),
            itemCount: snapshot.data!.length,
            separatorBuilder: (context, index) => const Divider(),
            itemBuilder: (context, index) {
              final item = snapshot.data![index];
              return ListTile(
                leading: const CircleAvatar(
                  backgroundColor: Color(0xFF008444),
                  child: Icon(Icons.call, color: Colors.white),
                ),
                title: Text(item['service_name'] ?? 'Service', style: const TextStyle(fontWeight: FontWeight.bold)),
                subtitle: Text(item['phone_number'] ?? ''),
                trailing: IconButton(
                  icon: const Icon(Icons.phone_in_talk, color: Colors.green, size: 28),
                  onPressed: () => _makeCall(item['phone_number']),
                ),
                onTap: () => _makeCall(item['phone_number']),
              );
            },
          );
        },
      ),
    );
  }
}