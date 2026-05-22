import 'package:flutter/material.dart';

class ReportDetailsScreen extends StatelessWidget {
  final Map<String, dynamic> report;

  const ReportDetailsScreen({super.key, required this.report});

  @override
  Widget build(BuildContext context) {
    // Access the media array from your Laravel relation
    final List<dynamic> mediaList = report['media'] ?? [];

    return Scaffold(
      appBar: AppBar(
        title: const Text("Report Details"),
        backgroundColor: const Color(0xFF008444),
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Tracking Number
            Text(report['tracking_number'] ?? 'N/A',
                style: const TextStyle(fontSize: 14, color: Colors.grey, fontWeight: FontWeight.bold)),
            Text(report['title'] ?? 'No Title',
                style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),

            const SizedBox(height: 10),
            Chip(
              label: Text(report['status']?.toString().toUpperCase() ?? 'PENDING'),
              backgroundColor: const Color(0xFF008444).withOpacity(0.1),
            ),

            const Divider(height: 30),

            const Text("Description:", style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 5),
            Text(report['description'] ?? 'No description provided.'),

            const SizedBox(height: 20),
            Text("Category: ${report['category'] ?? 'N/A'}"),
            Text("Location: ${report['location'] ?? 'N/A'}"),

            const SizedBox(height: 20),
            const Text("Attached Photos:", style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),

            // Image Gallery
            mediaList.isNotEmpty
                ? SizedBox(
              height: 150,
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                itemCount: mediaList.length,
                itemBuilder: (context, index) {
                  // Adjust path based on your ReportMedia model structure
                  final imageUrl = "http://192.168.43.123:8000/storage/${mediaList[index]['file_path']}";
                  return Padding(
                    padding: const EdgeInsets.only(right: 10),
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(8),
                      child: Image.network(
                        imageUrl,
                        width: 150,
                        height: 150,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) =>
                            Container(width: 150, color: Colors.grey[200], child: const Icon(Icons.broken_image)),
                      ),
                    ),
                  );
                },
              ),
            )
                : const Text("No photos attached."),
          ],
        ),
      ),
    );
  }
}