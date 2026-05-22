import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'alert_details_screen.dart';

class AlertsScreen extends StatefulWidget {
  const AlertsScreen({super.key});

  @override
  State<AlertsScreen> createState() => _AlertsScreenState();
}

class _AlertsScreenState extends State<AlertsScreen> {
  final ApiService _apiService = ApiService();
  late Future<List<dynamic>> _alertsFuture;

  // Nairobi County Brand Colors
  final Color _nairobiGreen = const Color(0xFF068930);
  final Color _nairobiYellow = const Color(0xFFFCDD07);
  final Color _nairobiBlue = const Color(0xFF0F47AF);

  @override
  void initState() {
    super.initState();
    _alertsFuture = _apiService.getAlerts();
  }

  Color _getTypeColor(String type) {
    switch (type.toLowerCase()) {
      case 'emergency': return Colors.redAccent;
      case 'maintenance': return _nairobiYellow;
      default: return _nairobiBlue;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text("County Alerts", style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: _nairobiGreen,
        foregroundColor: Colors.white,
      ),
      body: FutureBuilder<List<dynamic>>(
        future: _alertsFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator(color: _nairobiGreen));
          }
          if (snapshot.hasError) {
            return const Center(child: Text("Unable to load alerts."));
          }
          if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return const Center(child: Text("No active alerts."));
          }

          return ListView.builder(
            padding: const EdgeInsets.all(12),
            itemCount: snapshot.data!.length,
            itemBuilder: (context, index) {
              final alert = snapshot.data![index];
              return Card(
                elevation: 3,
                margin: const EdgeInsets.only(bottom: 12),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                child: ListTile(
                  leading: CircleAvatar(
                    backgroundColor: _getTypeColor(alert['type'] ?? '').withOpacity(0.15),
                    child: Icon(Icons.info_outline, color: _getTypeColor(alert['type'] ?? '')),
                  ),
                  title: Text(
                      alert['title'] ?? 'No Title',
                      style: const TextStyle(fontWeight: FontWeight.bold)
                  ),
                  subtitle: Text(
                    alert['content'] ?? '',
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  trailing: Icon(Icons.chevron_right, color: _nairobiGreen),
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => AlertDetailsScreen(
                          alert: Map<String, dynamic>.from(alert),
                        ),
                      ),
                    );
                  },
                ),
              );
            },
          );
        },
      ),
    );
  }
}