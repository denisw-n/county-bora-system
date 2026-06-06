import 'package:flutter/material.dart';
import '../config/api_constants.dart';
import '../services/api_service.dart';

class ReportDetailsScreen extends StatefulWidget {
  final Map<String, dynamic> report;
  const ReportDetailsScreen({super.key, required this.report});

  @override
  State<ReportDetailsScreen> createState() => _ReportDetailsScreenState();
}

class _ReportDetailsScreenState extends State<ReportDetailsScreen> {
  int _rating = 0;
  bool _isSubmitting = false;
  final TextEditingController _commentController = TextEditingController();

  Future<void> _submitRating() async {
    setState(() => _isSubmitting = true);

    // FIX: Get the ID as a String to correctly handle UUIDs
    final String reportId = widget.report['id'].toString();

    // Pass the String reportId and comment to the API service
    final result = await ApiService().submitReportRating(
      reportId,
      _rating,
      comment: _commentController.text,
    );

    if (mounted) {
      setState(() => _isSubmitting = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? "Rating submitted successfully!")),
      );
    }
  }

  @override
  void dispose() {
    _commentController.dispose();
    super.dispose();
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'resolved': return Colors.green;
      case 'in-progress': return Colors.orange;
      default: return Colors.blueGrey;
    }
  }

  @override
  Widget build(BuildContext context) {
    final List<dynamic> mediaList = widget.report['media'] ?? [];
    final String status = widget.report['status']?.toString().toUpperCase() ?? 'PENDING';

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
                  Text(widget.report['tracking_number'] ?? 'N/A', style: TextStyle(color: Colors.grey[600], fontWeight: FontWeight.w600)),
                  const SizedBox(height: 8),
                  Text(widget.report['title'] ?? 'No Title', style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: Color(0xFF333333))),
                  const SizedBox(height: 12),
                  Chip(
                    label: Text(status, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                    backgroundColor: _getStatusColor(status),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),
            const Text("Details", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            _buildInfoRow(Icons.category, "Category", widget.report['category'] ?? 'N/A'),
            _buildInfoRow(Icons.location_on, "Location", widget.report['location'] ?? 'N/A'),
            const SizedBox(height: 20),
            const Text("Description", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            Container(
              padding: const EdgeInsets.all(15),
              width: double.infinity,
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
              child: Text(widget.report['description'] ?? 'No description provided.', style: TextStyle(color: Colors.grey[800], height: 1.5)),
            ),
            const SizedBox(height: 20),
            if (mediaList.isNotEmpty) ...[
              const Text("Attached Photos", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              const SizedBox(height: 12),
              SizedBox(
                height: 160,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  itemCount: mediaList.length,
                  itemBuilder: (context, index) {
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
            ],
            if (status.trim().toUpperCase() == 'RESOLVED') ...[
              const SizedBox(height: 20),
              const Text("Rate this resolution", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              Row(
                children: List.generate(5, (index) {
                  return IconButton(
                    icon: Icon(index < _rating ? Icons.star : Icons.star_border, color: Colors.amber, size: 30),
                    onPressed: () => setState(() => _rating = index + 1),
                  );
                }),
              ),
              TextField(
                controller: _commentController,
                decoration: InputDecoration(
                  hintText: "Leave a comment (optional)",
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                  filled: true,
                  fillColor: Colors.white,
                ),
                maxLines: 3,
              ),
              const SizedBox(height: 15),
              ElevatedButton(
                onPressed: _rating > 0 && !_isSubmitting ? _submitRating : null,
                child: _isSubmitting
                    ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2))
                    : const Text("Submit Rating"),
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