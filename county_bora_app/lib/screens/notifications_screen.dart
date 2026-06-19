import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';
import 'notification_details_screen.dart';
import 'package:county_bora_app/widgets/app_refresh_indicator.dart';

class NotificationsScreen extends StatefulWidget {
  const NotificationsScreen({super.key});

  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  final ApiService _apiService = ApiService();

  List<dynamic> _notifications = [];
  bool _isLoading = true;

  // Nairobi County Brand Colors
  final Color _nairobiGreen = const Color(0xFF068930);
  final Color _nairobiYellow = const Color(0xFFFCDD07);

  @override
  void initState() {
    super.initState();
    _loadNotifications();
  }

  Future<void> _loadNotifications() async {
    setState(() => _isLoading = true);
    final data = await _apiService.getNotifications();
    if (mounted) {
      setState(() {
        _notifications = data;
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text("Notifications", style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: _nairobiGreen,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator(color: _nairobiGreen))
          : _notifications.isEmpty
          ? const Center(child: Text("You are all caught up!"))
          : AppRefreshIndicator(
        onRefresh: _loadNotifications,
        child: ListView.builder(
          padding: const EdgeInsets.all(16),
          physics: const AlwaysScrollableScrollPhysics(),
          itemCount: _notifications.length,
          itemBuilder: (context, index) {
            return _buildNotificationCard(_notifications[index]);
          },
        ),
      ),
    );
  }

  Widget _buildNotificationCard(dynamic note) {
    final bool isRead = note['is_read'] == 1 || note['is_read'] == true;
    final String status = (note['type'] ?? 'GENERAL').toUpperCase();

    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.08),
            blurRadius: 12,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: () async {
          if (!isRead) await _apiService.markNotificationAsRead(note['id']);
          _loadNotifications();
          if (mounted) {
            Navigator.push(context, MaterialPageRoute(
              builder: (_) => NotificationDetailsScreen(notification: Map<String, dynamic>.from(note)),
            ));
          }
        },
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            ClipRRect(
              borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
              child: Image.asset(
                'assets/images/city_hall.jpg',
                height: 140,
                width: double.infinity,
                fit: BoxFit.cover,
                errorBuilder: (_, __, ___) => Container(
                  height: 140,
                  color: Colors.grey[200],
                  child: Icon(Icons.location_city, color: _nairobiGreen, size: 40),
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: status.contains('SUCCESS')
                              ? Colors.green.withOpacity(0.1)
                              : _nairobiYellow.withOpacity(0.3),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(status, style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: status.contains('SUCCESS') ? Colors.green[800] : Colors.black87
                        )),
                      ),
                      Text(
                          _formatDate(note['created_at']),
                          style: TextStyle(fontSize: 10, color: Colors.grey[500])
                      ),
                    ],
                  ),
                  const SizedBox(height: 10),
                  Text(note['title'] ?? '', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  const SizedBox(height: 6),
                  Text(note['message'] ?? '',
                      style: TextStyle(fontSize: 13, color: Colors.grey[700]),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}