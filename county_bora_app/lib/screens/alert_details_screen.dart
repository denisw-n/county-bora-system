import 'package:flutter/material.dart';

class AlertDetailsScreen extends StatelessWidget {
  final Map<String, dynamic> alert;

  const AlertDetailsScreen({super.key, required this.alert});

  // Nairobi County Brand Colors
  final Color _nairobiGreen = const Color(0xFF068930);
  final Color _nairobiYellow = const Color(0xFFFCDD07);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Alert Details"),
        backgroundColor: _nairobiGreen,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Title
            Text(
              alert['title'] ?? 'No Title',
              style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: Colors.black87),
            ),
            const SizedBox(height: 15),

            // Type Badge
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: _nairobiYellow.withOpacity(0.3),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: _nairobiYellow),
              ),
              child: Text(
                (alert['type'] ?? 'General').toUpperCase(),
                style: TextStyle(fontWeight: FontWeight.bold, color: Colors.black87, fontSize: 12),
              ),
            ),

            const SizedBox(height: 25),

            // Content
            const Text("Details:", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 8),
            Text(
              alert['content'] ?? 'No content available.',
              style: const TextStyle(fontSize: 16, height: 1.5, color: Colors.black87),
            ),

            const SizedBox(height: 30),

            // Divider for clean separation
            Divider(color: Colors.grey[300]),
            const SizedBox(height: 10),

            // Optional: Author/Timestamp display
            if (alert['author'] != null)
              Text(
                "Posted by: ${alert['author']['first_name']} ${alert['author']['last_name']}",
                style: TextStyle(fontStyle: FontStyle.italic, color: Colors.grey[600]),
              ),
          ],
        ),
      ),
    );
  }
}