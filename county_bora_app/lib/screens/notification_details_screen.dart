import 'package:flutter/material.dart';

class NotificationDetailsScreen extends StatelessWidget {
  final Map<String, dynamic> notification;

  const NotificationDetailsScreen({super.key, required this.notification});

  @override
  Widget build(BuildContext context) {
    // Nairobi County Brand Colors
    const Color nairobiGreen = Color(0xFF068930);
    const Color nairobiYellow = Color(0xFFFCDD07);

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Notification Details"),
        backgroundColor: nairobiGreen,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Title
            Text(
              notification['title'] ?? 'No Title',
              style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: Colors.black87),
            ),
            const SizedBox(height: 15),

            // Type/Status Badge
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: nairobiYellow.withOpacity(0.3),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: nairobiYellow),
              ),
              child: Text(
                (notification['type'] ?? 'General').toUpperCase(),
                style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.black87, fontSize: 12),
              ),
            ),

            const SizedBox(height: 25),

            // Content
            const Text("Message:", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 8),
            Text(
              notification['message'] ?? 'No content available.',
              style: const TextStyle(fontSize: 16, height: 1.5, color: Colors.black87),
            ),

            const SizedBox(height: 30),

            // Footer Info
            Divider(color: Colors.grey[300]),
            const SizedBox(height: 10),
            Text(
              "Received on: ${notification['created_at'] ?? 'Date unknown'}",
              style: TextStyle(fontStyle: FontStyle.italic, color: Colors.grey[600]),
            ),
          ],
        ),
      ),
    );
  }
}