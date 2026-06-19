import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';
import 'alert_details_screen.dart';
import 'package:county_bora_app/widgets/app_refresh_indicator.dart';

class AlertsScreen extends StatefulWidget {
  const AlertsScreen({super.key});

  @override
  State<AlertsScreen> createState() => _AlertsScreenState();
}

class _AlertsScreenState extends State<AlertsScreen> {
  final ApiService _apiService = ApiService();

  List<dynamic> _alerts = [];
  bool _isLoading = true;

  // Nairobi County Brand Colors
  final Color _nairobiGreen = const Color(0xFF068930);
  final Color _nairobiYellow = const Color(0xFFFCDD07);
  final Color _nairobiBlue = const Color(0xFF0F47AF);

  @override
  void initState() {
    super.initState();
    _loadAlerts();
  }

  Future<void> _loadAlerts() async {
    setState(() => _isLoading = true);
    final data = await _apiService.getAlerts();
    if (mounted) {
      setState(() {
        _alerts = data;
        _isLoading = false;
      });
    }
  }

  String _formatDate(String? dateString) {
    if (dateString == null) return "Just now";
    try {
      DateTime dateTime = DateTime.parse(dateString);
      return DateFormat('MMM d, h:mm a').format(dateTime);
    } catch (e) {
      return "Recent";
    }
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
        elevation: 0,
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator(color: _nairobiGreen))
          : AppRefreshIndicator(
        onRefresh: _loadAlerts,
        child: ListView(
          physics: const AlwaysScrollableScrollPhysics(),
          children: [
            // Header Section
            SizedBox(
              height: 180,
              width: double.infinity,
              child: Stack(
                children: [
                  Image.asset(
                    'assets/images/city_banner.jpg',
                    width: double.infinity,
                    height: 180,
                    fit: BoxFit.cover,
                  ),
                  Container(color: Colors.black.withOpacity(0.5)),
                  Padding(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.end,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(color: _nairobiYellow, borderRadius: BorderRadius.circular(4)),
                          child: const Text("COUNTY UPDATES", style: TextStyle(color: Colors.black, fontSize: 10, fontWeight: FontWeight.bold)),
                        ),
                        const SizedBox(height: 10),
                        const Text(
                          "Stay informed about your city",
                          style: TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            // Alerts List
            Padding(
              padding: const EdgeInsets.all(12),
              child: _alerts.isEmpty
                  ? const Center(child: Padding(padding: EdgeInsets.only(top: 50), child: Text("No active alerts.")))
                  : Column(
                children: _alerts.map((alert) => Card(
                  elevation: 3,
                  margin: const EdgeInsets.only(bottom: 12),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  child: ListTile(
                    leading: CircleAvatar(
                      backgroundColor: _getTypeColor(alert['type'] ?? '').withOpacity(0.15),
                      child: Icon(Icons.info_outline, color: _getTypeColor(alert['type'] ?? '')),
                    ),
                    title: Text(alert['title'] ?? 'No Title', style: const TextStyle(fontWeight: FontWeight.bold)),
                    subtitle: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(alert['content'] ?? '', maxLines: 2, overflow: TextOverflow.ellipsis),
                        const SizedBox(height: 5),
                        Text(_formatDate(alert['created_at']), style: TextStyle(fontSize: 11, color: Colors.grey[600], fontStyle: FontStyle.italic)),
                      ],
                    ),
                    trailing: Icon(Icons.chevron_right, color: _nairobiGreen),
                    onTap: () {
                      Navigator.push(context, MaterialPageRoute(
                        builder: (context) => AlertDetailsScreen(alert: Map<String, dynamic>.from(alert)),
                      ));
                    },
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