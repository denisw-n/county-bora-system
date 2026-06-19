import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';
import 'report_details_screen.dart';
import 'package:county_bora_app/widgets/app_refresh_indicator.dart';

class ReportsHistoryScreen extends StatefulWidget {
  const ReportsHistoryScreen({super.key});

  @override
  State<ReportsHistoryScreen> createState() => _ReportsHistoryScreenState();
}

class _ReportsHistoryScreenState extends State<ReportsHistoryScreen> {
  final ApiService _apiService = ApiService();

  List<dynamic> _reports = [];
  bool _isLoading = true;

  final Color _nairobiGreen = const Color(0xFF068930);
  final Color _nairobiYellow = const Color(0xFFFCDD07);

  @override
  void initState() {
    super.initState();
    _loadReports();
  }

  Future<void> _loadReports() async {
    setState(() => _isLoading = true);
    final data = await _apiService.getAllReports();
    if (mounted) {
      setState(() {
        _reports = data;
        _isLoading = false;
      });
    }
  }

  String _formatDate(String? dateString) {
    if (dateString == null) return "Unknown date";
    try {
      return DateFormat('MMM d, yyyy').format(DateTime.parse(dateString));
    } catch (e) {
      return "Recent";
    }
  }

  Color _getStatusColor(String? status) {
    switch (status?.toLowerCase()) {
      case 'resolved': return _nairobiGreen;
      case 'in_progress': return _nairobiYellow;
      default: return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFE5E7EB),
      appBar: AppBar(
        title: const Text("My Reports", style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: _nairobiGreen,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator(color: _nairobiGreen))
          : AppRefreshIndicator(
        onRefresh: _loadReports,
        child: ListView(
          physics: const AlwaysScrollableScrollPhysics(),
          children: [
            // Header Section
            SizedBox(
              height: 160,
              width: double.infinity,
              child: Stack(
                children: [
                  Image.asset('assets/images/city_banner.jpg', width: double.infinity, height: 160, fit: BoxFit.cover),
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
                          child: const Text("ISSUE TRACKER", style: TextStyle(color: Colors.black, fontSize: 10, fontWeight: FontWeight.bold)),
                        ),
                        const SizedBox(height: 8),
                        const Text("Track your report progress", style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            // Report List
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              child: _reports.isEmpty
                  ? const Center(child: Padding(padding: EdgeInsets.only(top: 50), child: Text("No report history found.")))
                  : Column(
                children: _reports.map((report) => _buildReportCard(report)).toList(),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildReportCard(dynamic report) {
    final String status = report['status']?.toUpperCase() ?? 'PENDING';
    final Color statusColor = _getStatusColor(report['status']);

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.08), blurRadius: 8, offset: const Offset(0, 4))],
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: () => Navigator.push(context, MaterialPageRoute(
          builder: (_) => ReportDetailsScreen(report: Map<String, dynamic>.from(report)),
        )),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                    color: _nairobiGreen.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12)
                ),
                child: Icon(Icons.assignment_outlined, color: _nairobiGreen),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(report['title'] ?? 'No Title', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                    const SizedBox(height: 4),
                    Text(_formatDate(report['created_at']), style: TextStyle(fontSize: 12, color: Colors.grey[700])),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                    color: statusColor.withOpacity(0.15),
                    borderRadius: BorderRadius.circular(6)
                ),
                child: Text(
                    status,
                    style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: statusColor)
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}