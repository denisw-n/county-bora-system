import 'package:flutter/material.dart';
import '../config/api_constants.dart';
import '../services/api_service.dart'; // Added this import

class ReportDetailsScreen extends StatelessWidget {
  final Map<String, dynamic> report;

  const ReportDetailsScreen({super.key, required this.report});

  // Helper for status colors
  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'resolved': return Colors.green;
      case 'in-progress': return Colors.orange;
      default: return Colors.blueGrey;
    }
  }

  @override
  Widget build(BuildContext context) {
    final List<dynamic> mediaList = report['media'] ?? [];
    final String status = report['status']?.toString().toUpperCase() ?? 'PENDING';

    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: const Text("Report Details", style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: const Color(0xFF008444),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header Card - Now Pale Yellow
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.amber[50],
                borderRadius: BorderRadius.circular(16),
                boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4))],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(report['tracking_number'] ?? 'N/A', style: TextStyle(color: Colors.grey[600], fontWeight: FontWeight.w600)),
                  const SizedBox(height: 8),
                  Text(report['title'] ?? 'No Title', style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: Color(0xFF333333))),
                  const SizedBox(height: 12),
                  Chip(
                    label: Text(status, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                    backgroundColor: _getStatusColor(status),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 20),

            // Info Section
            const Text("Details", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            _buildInfoRow(Icons.category, "Category", report['category'] ?? 'N/A'),
            _buildInfoRow(Icons.location_on, "Location", report['location'] ?? 'N/A'),

            const SizedBox(height: 20),

            const Text("Description", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            Container(
              padding: const EdgeInsets.all(15),
              width: double.infinity,
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
              child: Text(report['description'] ?? 'No description provided.', style: TextStyle(color: Colors.grey[800], height: 1.5)),
            ),

            const SizedBox(height: 20),

            // Photos Section
            if (mediaList.isNotEmpty) ...[
              const Text("Attached Photos", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              const SizedBox(height: 12),
              SizedBox(
                height: 160,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  itemCount: mediaList.length,
                  itemBuilder: (context, index) {
                    // FIXED: Using our new helper to get the correct URL
                    final imageUrl = ApiService().getImageUrl(mediaList[index]['file_path']);

                    return Padding(
                      padding: const EdgeInsets.only(right: 12),
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(12),
                        child: Image.network(imageUrl, width: 140, height: 160, fit: BoxFit.cover),
                      ),
                    );
                  },
                ),
              ),
            ]
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(
        children: [
          Icon(icon, size: 20, color: const Color(0xFF008444)),
          const SizedBox(width: 10),
          Text("$label: ", style: const TextStyle(fontWeight: FontWeight.bold)),
          Expanded(child: Text(value, style: const TextStyle(color: Colors.black87))),
        ],
      ),
    );
  }
}