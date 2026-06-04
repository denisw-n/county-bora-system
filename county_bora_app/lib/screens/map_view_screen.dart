import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import '../services/api_service.dart';
import 'report_details_screen.dart';

class MapViewScreen extends StatefulWidget {
  const MapViewScreen({super.key});

  @override
  State<MapViewScreen> createState() => _MapViewScreenState();
}

class _MapViewScreenState extends State<MapViewScreen> {
  final ApiService _apiService = ApiService();
  List<dynamic> _allReports = [];
  List<Marker> _markers = [];
  bool _isLoading = true;
  String _selectedStatus = 'Show All';
  final Color _countyGreen = const Color(0xFF008444);

  final List<String> _filters = ['Show All', 'Pending', 'Dispatched', 'In Progress', 'Resolved'];

  @override
  void initState() {
    super.initState();
    _loadAllReports();
  }

  Future<void> _loadAllReports() async {
    final response = await _apiService.getMyMapMarkers();
    if (response['status'] == 'success') {
      setState(() {
        _allReports = response['data'];
        _applyFilter(_selectedStatus);
        _isLoading = false;
      });
    } else {
      setState(() => _isLoading = false);
    }
  }

  void _applyFilter(String status) {
    setState(() {
      _selectedStatus = status;
      List<dynamic> filtered = status == 'Show All'
          ? _allReports
          : _allReports.where((r) => (r['status'] ?? '').toString().toLowerCase() == status.toLowerCase()).toList();

      _markers = filtered.map((report) {
        return Marker(
          point: LatLng(double.parse(report['latitude'].toString()), double.parse(report['longitude'].toString())),
          width: 50,
          height: 50,
          child: GestureDetector(
            onTap: () async {
              // Show loading indicator
              showDialog(
                context: context,
                barrierDismissible: false,
                builder: (context) => const Center(child: CircularProgressIndicator(color: Colors.white)),
              );

              try {
                // Fetch full report data using the ID
                final fullReport = await _apiService.getReportById(report['id']);

                if (mounted) Navigator.pop(context); // Remove loading

                if (mounted) {
                  Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (context) => ReportDetailsScreen(
                              report: Map<String, dynamic>.from(fullReport)
                          )
                      )
                  );
                }
              } catch (e) {
                if (mounted) Navigator.pop(context); // Remove loading
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text("Failed to load report details.")),
                );
              }
            },
            child: Icon(Icons.location_on, color: _getMarkerColor(report['status'] ?? ''), size: 40),
          ),
        );
      }).toList();
    });
  }

  Color _getMarkerColor(String status) {
    switch (status.toLowerCase()) {
      case 'pending': return Colors.red;
      case 'dispatched': return Colors.blue;
      case 'in progress': return Colors.orange;
      case 'resolved': return Colors.green;
      default: return _countyGreen;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("My Reports Map", style: TextStyle(color: Colors.white)),
        backgroundColor: _countyGreen,
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator(color: _countyGreen))
          : Column(
        children: [
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
            child: Row(
              children: _filters.map((filter) => Padding(
                padding: const EdgeInsets.symmetric(horizontal: 4),
                child: ChoiceChip(
                  label: Text(filter),
                  selected: _selectedStatus == filter,
                  onSelected: (selected) => _applyFilter(filter),
                  selectedColor: _countyGreen,
                  labelStyle: TextStyle(color: _selectedStatus == filter ? Colors.white : Colors.black),
                ),
              )).toList(),
            ),
          ),
          Expanded(
            child: FlutterMap(
              options: const MapOptions(initialCenter: LatLng(-1.2921, 36.8219), initialZoom: 12.0),
              children: [
                TileLayer(
                  urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                  userAgentPackageName: 'com.countybora.nairobi.portal',
                ),
                MarkerLayer(markers: _markers),
              ],
            ),
          ),
        ],
      ),
    );
  }
}