import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:geolocator/geolocator.dart';
import 'package:http/http.dart' as http;
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import 'dart:convert';

import '../config/api_constants.dart';

class ReportIssueScreen extends StatefulWidget {
  final String token;
  final Map<String, dynamic> userData;

  const ReportIssueScreen({super.key, required this.token, required this.userData});

  @override
  State<ReportIssueScreen> createState() => _ReportIssueScreenState();
}

class _ReportIssueScreenState extends State<ReportIssueScreen> {
  final TextEditingController _descriptionController = TextEditingController();

  List<dynamic> _departments = [];
  String? _selectedDeptName;
  File? _imageFile;

  final LatLng _defaultLocation = const LatLng(-1.286389, 36.817223);
  late LatLng _pickedLocation;

  bool _isLoading = true;
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    _pickedLocation = _defaultLocation;

    WidgetsBinding.instance.addPostFrameCallback((_) {
      _fetchDepartments();
    });
  }

  Future<void> _fetchDepartments() async {
    try {
      final response = await http.get(
        Uri.parse("${ApiConstants.baseUrl}/departments"),
        headers: {
          'Authorization': 'Bearer ${widget.token.trim()}',
          'Accept': 'application/json',
        },
      ).timeout(const Duration(seconds: 10));

      debugPrint("🔍 API DEPARTMENTS RESPONSE: ${response.statusCode} - ${response.body}");

      if (!mounted) return;

      if (response.statusCode == 200) {
        final dynamic decodedData = json.decode(response.body);

        setState(() {
          if (decodedData is List) {
            _departments = decodedData;
          } else if (decodedData is Map && decodedData.containsKey('data')) {
            _departments = decodedData['data'];
          } else {
            debugPrint("❌ UNEXPECTED DATA STRUCTURE: $decodedData");
            _showSnackBar("Unexpected data format from server.");
          }
        });
      } else if (response.statusCode == 401) {
        _showSnackBar("Session expired. Please log in again.");
      } else {
        _showSnackBar("Server error: ${response.statusCode}");
      }
    } catch (e) {
      debugPrint("❌ FETCH DEPARTMENTS ERROR: $e");
      if (mounted) _showSnackBar("Connection error. Check your server.");
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _showSnackBar(String message) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(message),
      behavior: SnackBarBehavior.floating,
    ));
  }

  Future<void> _pickImage() async {
    final ImagePicker picker = ImagePicker();
    final XFile? image = await showModalBottomSheet<XFile>(
      context: context,
      builder: (context) => SafeArea(
        child: Wrap(
          children: [
            ListTile(
              leading: const Icon(Icons.photo_library),
              title: const Text('Gallery'),
              onTap: () async => Navigator.pop(context, await picker.pickImage(source: ImageSource.gallery)),
            ),
            ListTile(
              leading: const Icon(Icons.camera_alt),
              title: const Text('Camera'),
              onTap: () async => Navigator.pop(context, await picker.pickImage(source: ImageSource.camera)),
            ),
          ],
        ),
      ),
    );

    if (image != null) setState(() => _imageFile = File(image.path));
  }

  Future<void> _openMapPicker() async {
    final LatLng? result = await Navigator.push(
      context,
      MaterialPageRoute(builder: (context) => FullMapPicker(initialLocation: _pickedLocation)),
    );
    if (result != null) setState(() => _pickedLocation = result);
  }

  Future<void> _submitReport() async {
    if (_selectedDeptName == null || _descriptionController.text.trim().isEmpty) {
      _showSnackBar("Please select a category and add a description");
      return;
    }

    setState(() => _isSubmitting = true);

    try {
      final uri = Uri.parse("${ApiConstants.baseUrl}/reports");
      var request = http.MultipartRequest('POST', uri);

      request.headers.addAll({
        'Authorization': 'Bearer ${widget.token.trim()}',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      });

      request.fields['title'] = "Incident: $_selectedDeptName";
      request.fields['category'] = _selectedDeptName!;
      request.fields['description'] = _descriptionController.text.trim();
      request.fields['location'] = "Pinned via Map";
      request.fields['latitude'] = _pickedLocation.latitude.toString();
      request.fields['longitude'] = _pickedLocation.longitude.toString();

      if (widget.userData['ward_id'] != null) {
        request.fields['ward_id'] = widget.userData['ward_id'].toString();
      }

      if (_imageFile != null) {
        request.files.add(await http.MultipartFile.fromPath(
            'images[]',
            _imageFile!.path
        ));
      }

      var streamedResponse = await request.send();
      var response = await http.Response.fromStream(streamedResponse);

      if (!mounted) return;

      if (response.statusCode == 201 || response.statusCode == 200) {
        _showSnackBar("Report submitted successfully!");
        Navigator.pop(context);
      } else if (response.statusCode == 422) {
        final errors = json.decode(response.body);
        _showSnackBar(errors['message'] ?? "Validation failed");
      } else {
        _showSnackBar("Failed to submit (Error ${response.statusCode})");
      }
    } catch (e) {
      if (mounted) _showSnackBar("Submission error. Check network.");
    } finally {
      if (mounted) setState(() => _isSubmitting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    const primaryGreen = Color(0xFF008444);

    if (_isLoading) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator(color: primaryGreen)),
      );
    }

    bool isLocationPicked = _pickedLocation != _defaultLocation;

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('Submit Report', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        backgroundColor: primaryGreen,
        iconTheme: const IconThemeData(color: Colors.white),
        centerTitle: true,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 20),
            _buildLabel("SELECT CATEGORY"),
            DropdownButtonFormField<String>(
              value: _selectedDeptName,
              hint: const Text("Select a service type"),
              isExpanded: true,
              decoration: _inputDecoration(),
              items: _departments.map<DropdownMenuItem<String>>((dept) {
                String dName = dept['dept_name']?.toString() ?? 'Unknown';
                return DropdownMenuItem<String>(value: dName, child: Text(dName));
              }).toList(),
              onChanged: (val) => setState(() => _selectedDeptName = val),
            ),
            const SizedBox(height: 25),
            _buildLabel("DESCRIPTION OF THE ISSUE"),
            TextField(
              controller: _descriptionController,
              maxLines: 4,
              decoration: _inputDecoration(hint: "Detailed description of the problem..."),
            ),
            const SizedBox(height: 25),
            _buildLabel("UPLOAD IMAGE/VIDEO"),
            GestureDetector(
              onTap: _pickImage,
              child: Container(
                width: double.infinity,
                height: 120,
                decoration: BoxDecoration(
                  border: Border.all(color: Colors.grey.shade300),
                  borderRadius: BorderRadius.circular(8),
                  color: Colors.grey.shade50,
                  image: _imageFile != null ? DecorationImage(image: FileImage(_imageFile!), fit: BoxFit.cover) : null,
                ),
                child: _imageFile == null ? Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(color: const Color(0xFFFFD700), borderRadius: BorderRadius.circular(8)),
                      child: const Icon(Icons.camera_alt, color: Colors.black),
                    ),
                    const SizedBox(height: 8),
                    const Text("Tap to capture or upload media", style: TextStyle(fontSize: 12, color: Colors.grey)),
                  ],
                ) : null,
              ),
            ),
            const SizedBox(height: 25),
            _buildLabel("ISSUE LOCATION"),
            InkWell(
              onTap: _openMapPicker,
              child: Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  border: Border.all(color: Colors.grey.shade300),
                  borderRadius: BorderRadius.circular(8),
                  color: Colors.grey.shade50,
                ),
                child: Row(
                  children: [
                    const Icon(Icons.location_on, color: primaryGreen),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Text(
                        isLocationPicked
                            ? "Location: ${_pickedLocation.latitude.toStringAsFixed(4)}, ${_pickedLocation.longitude.toStringAsFixed(4)}"
                            : "Open map to pick location",
                        style: TextStyle(color: isLocationPicked ? Colors.black87 : Colors.grey.shade600),
                      ),
                    ),
                    const Icon(Icons.chevron_right, color: Colors.grey),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 40),
            _isSubmitting
                ? const Center(child: CircularProgressIndicator(color: primaryGreen))
                : ElevatedButton(
              onPressed: _submitReport,
              style: ElevatedButton.styleFrom(
                backgroundColor: primaryGreen,
                foregroundColor: Colors.white,
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
              ),
              child: const Text("SUBMIT REPORT", style: TextStyle(fontWeight: FontWeight.bold)),
            ),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }

  Widget _buildLabel(String text) => Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Text(text, style: const TextStyle(color: Color(0xFF008444), fontWeight: FontWeight.bold, fontSize: 12))
  );

  InputDecoration _inputDecoration({String? hint}) => InputDecoration(
    hintText: hint,
    filled: true,
    fillColor: Colors.white,
    enabledBorder: OutlineInputBorder(borderSide: BorderSide(color: Colors.grey.shade300), borderRadius: BorderRadius.circular(8)),
    focusedBorder: OutlineInputBorder(borderSide: const BorderSide(color: Color(0xFF008444), width: 2), borderRadius: BorderRadius.circular(8)),
  );
}

class FullMapPicker extends StatefulWidget {
  final LatLng initialLocation;
  const FullMapPicker({super.key, required this.initialLocation});

  @override
  State<FullMapPicker> createState() => _FullMapPickerState();
}

class _FullMapPickerState extends State<FullMapPicker> {
  late LatLng _currentPoint;
  final MapController _mapController = MapController();
  bool _isLocating = false;

  @override
  void initState() {
    super.initState();
    _currentPoint = widget.initialLocation;
  }

  Future<void> _handleLocateMe() async {
    setState(() => _isLocating = true);
    try {
      Position position = await Geolocator.getCurrentPosition(desiredAccuracy: LocationAccuracy.high);
      setState(() {
        _currentPoint = LatLng(position.latitude, position.longitude);
        _mapController.move(_currentPoint, 16.0);
      });
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Could not get location")));
    } finally {
      if (mounted) setState(() => _isLocating = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    const primaryGreen = Color(0xFF008444);
    return Scaffold(
      appBar: AppBar(
        title: const Text("Pin Issue Location", style: TextStyle(color: Colors.white, fontSize: 18)),
        backgroundColor: primaryGreen,
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, _currentPoint),
            child: const Text("DONE", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          )
        ],
      ),
      body: Stack(
        children: [
          FlutterMap(
            mapController: _mapController,
            options: MapOptions(
              initialCenter: _currentPoint,
              initialZoom: 15.0,
              onTap: (tapPos, point) => setState(() => _currentPoint = point),
            ),
            children: [
              TileLayer(
                urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                userAgentPackageName: 'com.wanjala.county_bora_app',
              ),
              MarkerLayer(markers: [
                Marker(
                    point: _currentPoint,
                    width: 50,
                    height: 50,
                    child: const Icon(Icons.location_on, color: Colors.red, size: 45)
                ),
              ]),
            ],
          ),
          Positioned(
            top: 15,
            left: 15,
            right: 15,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.9),
                borderRadius: BorderRadius.circular(8),
                boxShadow: const [BoxShadow(color: Colors.black26, blurRadius: 4)],
              ),
              child: const Text(
                "Tap anywhere on the map to pin your location manually.",
                textAlign: TextAlign.center,
                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: primaryGreen),
              ),
            ),
          ),
          Positioned(
            bottom: 30,
            right: 20,
            child: FloatingActionButton.extended(
              onPressed: _isLocating ? null : _handleLocateMe,
              backgroundColor: primaryGreen,
              icon: _isLocating
                  ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                  : const Icon(Icons.my_location, color: Colors.white),
              label: const Text("Locate Me", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ),
        ],
      ),
    );
  }
}