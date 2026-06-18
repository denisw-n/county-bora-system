import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_constants.dart';
import '../main.dart';

class ApiService {
  final String baseUrl = ApiConstants.baseUrl;

  // This helper correctly fixes image paths for you
  String getImageUrl(String? path) {
    if (path == null || path.isEmpty) return '';
    if (path.startsWith('http')) return path;
    // This uses the root URL without the '/api' part
    return '${ApiConstants.storageUrl}/storage/$path';
  }

  // =========================
  // TRANSPARENCY PORTAL
  // =========================
  Future<Map<String, dynamic>> getTransparencyStats() async {
    try {
      final headers = await _getAuthHeaders();
      final response = await http.get(
        Uri.parse('$baseUrl/transparency'),
        headers: headers,
      );

      // --- DEBUG PRINT ADDED HERE ---
      debugPrint("🔍 API DEBUG - Full Response: ${response.body}");

      if (await _handle401(response)) {
        return {'error': 'Unauthorized'};
      }

      if (response.statusCode == 200) {
        // Returned directly to allow TransparencyScreen to handle real API keys
        return Map<String, dynamic>.from(jsonDecode(response.body));
      }
      return {'error': 'Failed to load stats. Status: ${response.statusCode}'};
    } catch (e) {
      debugPrint("❌ TRANSPARENCY STATS ERROR: $e");
      return {'error': 'Connection error: $e'};
    }
  }

  // =========================
  // FORGOT PASSWORD
  // =========================
  Future<Map<String, dynamic>> forgotPassword(String email) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/forgot-password'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: jsonEncode({'email': email}),
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        return {'error': 'Failed to send link. Please check your email.'};
      }
    } catch (e) {
      return {'error': 'Connection error: $e'};
    }
  }

  // =========================
  // AUTH HEADERS
  // =========================
  Future<Map<String, String>> _getAuthHeaders() async {
    final token = await _getSavedToken();
    return {
      'Accept': 'application/json',
      'Authorization': 'Bearer ${token?.trim()}',
      'X-Requested-With': 'XMLHttpRequest',
    };
  }

  // =========================
  // SESSION HANDLER
  // =========================
  Future<bool> _handle401(http.Response response) async {
    if (response.statusCode == 401) {
      await logout();
      navigatorKey.currentState?.pushNamedAndRemoveUntil('/login', (route) => false);
      return true;
    }
    return false;
  }

  // =========================
  // TOKEN & USER STORAGE
  // =========================
  Future<String?> _getSavedToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }

  Future<void> _saveAuthData(String token, Map<String, dynamic> user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
    await prefs.setString('user_data', jsonEncode(user));
  }

  Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    await prefs.remove('user_data');
    debugPrint("🚪 SESSION CLEARED");
  }

  // =========================
  // MAP MARKERS
  // =========================
  Future<Map<String, dynamic>> getMyMapMarkers() async {
    try {
      final headers = await _getAuthHeaders();
      final response = await http.get(
        Uri.parse('$baseUrl/my-map-markers'),
        headers: headers,
      );

      if (await _handle401(response)) return {'status': 'error', 'data': []};
      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
      return {'status': 'error', 'data': []};
    } catch (e) {
      debugPrint("❌ MAP MARKERS ERROR: $e");
      return {'status': 'error', 'data': []};
    }
  }

  // =========================
  // FETCH SINGLE REPORT BY ID
  // =========================
  Future<Map<String, dynamic>> getReportById(String reportId) async {
    try {
      final headers = await _getAuthHeaders();
      final url = Uri.parse('$baseUrl/my-reports/$reportId');
      final response = await http.get(url, headers: headers);

      if (response.statusCode != 200) {
        debugPrint("❌ API ERROR: Status Code ${response.statusCode}");
        debugPrint("❌ URL TRIED: $url");
        throw Exception('Failed to load report. Status: ${response.statusCode}');
      }

      final body = jsonDecode(response.body);
      return Map<String, dynamic>.from(body['data'] ?? {});
    } catch (e) {
      debugPrint("❌ FETCH REPORT BY ID ERROR: $e");
      rethrow;
    }
  }

  // =========================
  // PROFILE
  // =========================
  Future<Map<String, dynamic>> getProfile() async {
    try {
      final headers = await _getAuthHeaders();
      final response = await http.get(
        Uri.parse('$baseUrl/profile'),
        headers: headers,
      );

      if (await _handle401(response)) return {'error': 'Unauthorized'};
      if (response.statusCode == 200) {
        return jsonDecode(response.body)['data'];
      }
      return {'error': 'Failed to load profile'};
    } catch (e) {
      return {'error': 'Connection error: $e'};
    }
  }

  Future<Map<String, dynamic>> updateProfile({
    String? phoneNumber,
    String? wardId,
  }) async {
    try {
      final headers = await _getAuthHeaders();
      headers['Content-Type'] = 'application/json';

      final body = <String, String>{};
      if (phoneNumber != null) body['phone_number'] = phoneNumber;
      if (wardId != null) body['ward_id'] = wardId;

      final response = await http.patch(
        Uri.parse('$baseUrl/profile/update'),
        headers: headers,
        body: jsonEncode(body),
      );

      if (await _handle401(response)) return {'error': 'Unauthorized'};
      return jsonDecode(response.body);
    } catch (e) {
      return {'error': 'Update failed: $e'};
    }
  }

  // =========================
  // WARDS (PUBLIC)
  // =========================
  Future<List<dynamic>> getWards() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/wards'),
        headers: {'Accept': 'application/json'},
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data is Map ? data['data'] ?? [] : data;
      }
      return [];
    } catch (e) {
      debugPrint("❌ WARDS ERROR: $e");
      return [];
    }
  }

  // =========================
  // HOTLINES
  // =========================
  Future<List<dynamic>> getHotlines() async {
    try {
      final headers = await _getAuthHeaders();
      final response = await http.get(
        Uri.parse('$baseUrl/hotlines'),
        headers: headers,
      );

      if (await _handle401(response)) return [];
      if (response.statusCode == 200) {
        final body = jsonDecode(response.body);
        return body['data'] ?? [];
      }
      return [];
    } catch (e) {
      debugPrint("❌ HOTLINES ERROR: $e");
      return [];
    }
  }

  // =========================
  // ALERTS
  // =========================
  Future<List<dynamic>> getAlerts() async {
    try {
      final headers = await _getAuthHeaders();
      final response = await http.get(
        Uri.parse('$baseUrl/alerts'),
        headers: headers,
      );

      if (await _handle401(response)) return [];
      if (response.statusCode == 200) {
        final body = jsonDecode(response.body);
        return body['alerts'] ?? [];
      }
      return [];
    } catch (e) {
      debugPrint("❌ ALERTS ERROR: $e");
      return [];
    }
  }

  // =========================
  // NOTIFICATIONS
  // =========================
  Future<List<dynamic>> getNotifications() async {
    try {
      final headers = await _getAuthHeaders();
      final response = await http.get(
        Uri.parse('$baseUrl/notifications'),
        headers: headers,
      );

      if (await _handle401(response)) return [];
      if (response.statusCode == 200) {
        final body = jsonDecode(response.body);
        return body['notifications'] ?? [];
      }
      return [];
    } catch (e) {
      debugPrint("❌ NOTIFICATIONS ERROR: $e");
      return [];
    }
  }

  Future<void> markNotificationAsRead(int id) async {
    try {
      final headers = await _getAuthHeaders();
      final response = await http.patch(
        Uri.parse('$baseUrl/notifications/$id/read'),
        headers: headers,
      );
      await _handle401(response);
    } catch (e) {
      debugPrint("❌ MARK AS READ ERROR: $e");
    }
  }

  Future<int> getUnreadNotificationCount() async {
    try {
      final headers = await _getAuthHeaders();
      final response = await http.get(
        Uri.parse('$baseUrl/notifications/unread-count'),
        headers: headers,
      );

      if (await _handle401(response)) return 0;
      if (response.statusCode == 200) {
        final body = jsonDecode(response.body);
        return body['unread_count'] ?? 0;
      }
      return 0;
    } catch (e) {
      return 0;
    }
  }

  // =========================
  // LOGIN
  // =========================
  Future<Map<String, dynamic>> loginUser({
    required String login,
    required String password,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'login': login,
          'password': password,
          'device_name': 'TECNO_KB8',
        }),
      );

      final body = jsonDecode(response.body);
      if (response.statusCode == 200 && body['access_token'] != null) {
        await _saveAuthData(body['access_token'], body['user'] ?? {});
        return body;
      }
      return {'error': body['message'] ?? 'Login failed'};
    } catch (e) {
      return {'error': 'Connection error: $e'};
    }
  }

  // =========================
  // REGISTER
  // =========================
  Future<Map<String, dynamic>> registerUser({
    required String firstName,
    String? middleName,
    required String lastName,
    required String email,
    required String password,
    required String confirmPassword,
    required String nationalId,
    required String phoneNumber,
    required String wardId,
    required File idImage,
  }) async {
    try {
      var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/register'));
      request.headers['Accept'] = 'application/json';

      request.fields.addAll({
        'first_name': firstName,
        'last_name': lastName,
        'email': email,
        'national_id': nationalId,
        'phone_number': phoneNumber,
        'ward_id': wardId,
        'password': password,
        'password_confirmation': confirmPassword,
      });

      if (middleName != null && middleName.isNotEmpty) {
        request.fields['middle_name'] = middleName;
      }

      request.files.add(await http.MultipartFile.fromPath('national_id_image', idImage.path));

      final response = await http.Response.fromStream(await request.send());
      final body = jsonDecode(response.body);

      if (body is Map) return Map<String, dynamic>.from(body);
      return {'error': 'Unexpected server response structure'};
    } catch (e) {
      return {'error': 'Registration error: $e'};
    }
  }

  // =========================
  // SUBMIT REPORT
  // =========================
  Future<Map<String, dynamic>> submitReport({
    required String category,
    required String description,
    required String wardId,
    required double lat,
    required double lng,
    required List<File> images,
  }) async {
    try {
      final headers = await _getAuthHeaders();
      var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/reports'));
      request.headers.addAll(headers);

      request.fields.addAll({
        'title': "Incident: $category",
        'category': category,
        'description': description,
        'location': "Pinned via Map",
        'ward_id': wardId,
        'latitude': lat.toString(),
        'longitude': lng.toString(),
      });

      for (var image in images) {
        request.files.add(await http.MultipartFile.fromPath('images[]', image.path));
      }

      final response = await http.Response.fromStream(await request.send());
      if (response.statusCode == 401) {
        await logout();
        return {'status': 'error', 'message': 'Session expired. Please login again.'};
      }
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Submission failed: $e'};
    }
  }

  // =========================
  // FETCH RECENT REPORTS
  // =========================
  Future<List<dynamic>> getMyRecentReports({int limit = 5}) async {
    try {
      final headers = await _getAuthHeaders();
      final response = await http.get(
        Uri.parse('$baseUrl/my-reports?limit=$limit'),
        headers: headers,
      );

      if (await _handle401(response)) return [];
      if (response.statusCode == 200) {
        final body = jsonDecode(response.body);
        return body['data'] ?? [];
      }
      return [];
    } catch (e) {
      debugPrint("❌ FETCH REPORTS ERROR: $e");
      return [];
    }
  }

  // =========================
  // FETCH ALL REPORTS
  // =========================
  Future<List<dynamic>> getAllReports() async {
    try {
      final headers = await _getAuthHeaders();
      final response = await http.get(
        Uri.parse('$baseUrl/my-reports'),
        headers: headers,
      );

      if (await _handle401(response)) return [];
      if (response.statusCode == 200) {
        final body = jsonDecode(response.body);
        return body['data'] ?? [];
      }
      return [];
    } catch (e) {
      debugPrint("❌ FETCH ALL REPORTS ERROR: $e");
      return [];
    }
  }

  // =========================
  // SUBMIT RATING
  // =========================
  Future<Map<String, dynamic>> submitReportRating(String reportId, int rating, {String comment = ''}) async {
    try {
      final headers = await _getAuthHeaders();
      headers['Content-Type'] = 'application/json';

      final response = await http.post(
        Uri.parse('$baseUrl/reports/$reportId/rate'),
        headers: headers,
        body: jsonEncode({
          'stars': rating,
          'comment': comment
        }),
      );

      if (await _handle401(response)) {
        return {'status': 'error', 'message': 'Unauthorized'};
      }

      return jsonDecode(response.body);
    } catch (e) {
      debugPrint("❌ SUBMIT RATING ERROR: $e");
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }
}