import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/api_service.dart';
import 'report_issue_screen.dart';
import 'reports_history_screen.dart';
import 'report_details_screen.dart';
import 'hotlines_screen.dart';
import 'alerts_screen.dart';
import 'notifications_screen.dart';
import '../main.dart'; // Ensure this allows access to navigatorKey

class DashboardScreen extends StatefulWidget {
  final Map<dynamic, dynamic> userData;

  const DashboardScreen({super.key, required this.userData});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> with WidgetsBindingObserver {
  final ApiService _apiService = ApiService();
  String _authToken = '';
  Map<String, dynamic> _userProfile = {};
  bool _isLoading = true;
  late Future<List<dynamic>> _recentReportsFuture;
  int _unreadCount = 0;

  final Color _countyGreen = const Color(0xFF008444);
  final Color _cardGrey = const Color(0xFFF5F5F5);
  final Color _accentYellow = const Color(0xFFFFD700);

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    WidgetsBinding.instance.addPostFrameCallback((_) => _initializeData());
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      _verifySession();
    }
  }

  Future<void> _verifySession() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('auth_token');
    if (token == null) {
      navigatorKey.currentState?.pushNamedAndRemoveUntil('/login', (route) => false);
    }
  }

  void _initializeData() async {
    try {
      Map<String, dynamic> parsedProfile = {};
      if (widget.userData.containsKey('user') && widget.userData['user'] is Map) {
        widget.userData['user'].forEach((key, value) => parsedProfile[key.toString()] = value);
      } else {
        widget.userData.forEach((key, value) => parsedProfile[key.toString()] = value);
      }

      String token = widget.userData['access_token']?.toString() ?? widget.userData['token']?.toString() ?? '';
      if (token.isEmpty) {
        final prefs = await SharedPreferences.getInstance();
        token = prefs.getString('auth_token') ?? '';
      }

      int count = await _apiService.getUnreadNotificationCount();

      if (mounted) {
        setState(() {
          _userProfile = parsedProfile;
          _authToken = token;
          _recentReportsFuture = _apiService.getMyRecentReports(limit: 5);
          _unreadCount = count;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) return const Scaffold(body: Center(child: CircularProgressIndicator()));

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        automaticallyImplyLeading: false,
        title: Row(
          children: [
            Icon(Icons.menu, color: _countyGreen),
            const SizedBox(width: 15),
            Text("Nairobi Service Portal", style: TextStyle(color: _countyGreen, fontWeight: FontWeight.bold, fontSize: 18)),
          ],
        ),
        actions: [
          IconButton(
            icon: Stack(
              children: [
                Icon(Icons.notifications_none, color: _countyGreen),
                if (_unreadCount > 0)
                  Positioned(
                    right: 0,
                    child: Container(
                      padding: const EdgeInsets.all(2),
                      decoration: BoxDecoration(color: Colors.red, borderRadius: BorderRadius.circular(6)),
                      constraints: const BoxConstraints(minWidth: 12, minHeight: 12),
                      child: Text('$_unreadCount', style: const TextStyle(color: Colors.white, fontSize: 8), textAlign: TextAlign.center),
                    ),
                  )
              ],
            ),
            onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const NotificationsScreen())),
          ),
          Padding(
            padding: const EdgeInsets.only(right: 10),
            child: CircleAvatar(backgroundColor: _cardGrey, child: const Icon(Icons.person, color: Colors.grey)),
          )
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (c) => ReportIssueScreen(token: _authToken, userData: _userProfile))),
        backgroundColor: _accentYellow,
        icon: const Icon(Icons.add_circle_outline, color: Colors.black),
        label: const Text("REPORT AN ISSUE", style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold)),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text("Jambo, ${_userProfile['first_name'] ?? 'Citizen'}!", style: const TextStyle(fontSize: 16, color: Colors.grey)),
              const Text("What would you like to do today?", style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
              const SizedBox(height: 20),
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(color: _countyGreen, borderRadius: BorderRadius.circular(15)),
                child: Row(
                  children: [
                    const Icon(Icons.bar_chart, color: Colors.white, size: 40),
                    const SizedBox(width: 15),
                    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: const [
                      Text("Transparency Portal", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                      Text("Monitor city development & budget", style: TextStyle(color: Colors.white70, fontSize: 12)),
                    ])),
                    const Icon(Icons.arrow_forward_ios, color: Colors.white, size: 16),
                  ],
                ),
              ),
              const SizedBox(height: 25),
              Row(children: [
                Expanded(child: _buildActionCard("Reports History", Icons.history, () => Navigator.push(context, MaterialPageRoute(builder: (context) => const ReportsHistoryScreen())))),
                const SizedBox(width: 15),
                Expanded(child: _buildActionCard("Hotlines", Icons.call, () => Navigator.push(context, MaterialPageRoute(builder: (context) => const HotlinesScreen())))),
              ]),
              const SizedBox(height: 15),
              Row(children: [
                Expanded(child: _buildActionCard("Map View", Icons.map, () {})),
                const SizedBox(width: 15),
                Expanded(child: _buildActionCard("Alerts", Icons.notifications_active, () => Navigator.push(context, MaterialPageRoute(builder: (context) => const AlertsScreen())))),
              ]),
              const SizedBox(height: 30),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text("MY RECENT REPORTS", style: TextStyle(fontWeight: FontWeight.bold)),
                  TextButton(
                    onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const ReportsHistoryScreen())),
                    child: Text("View All", style: TextStyle(color: _countyGreen, fontWeight: FontWeight.bold)),
                  ),
                ],
              ),
              const SizedBox(height: 5),
              FutureBuilder<List<dynamic>>(
                future: _recentReportsFuture,
                builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.waiting) {
                    return const Center(child: Padding(padding: EdgeInsets.all(20), child: CircularProgressIndicator()));
                  } else if (snapshot.hasError || !snapshot.hasData || snapshot.data!.isEmpty) {
                    return const Padding(padding: EdgeInsets.symmetric(vertical: 10), child: Text("No recent reports found."));
                  }
                  return Column(
                    children: snapshot.data!.map((report) => _buildRecentReportCard(report)).toList(),
                  );
                },
              ),
              const SizedBox(height: 100),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildRecentReportCard(dynamic report) {
    return InkWell(
      onTap: () {
        Navigator.push(context, MaterialPageRoute(
            builder: (context) => ReportDetailsScreen(report: Map<String, dynamic>.from(report))
        ));
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(15),
        decoration: BoxDecoration(color: _cardGrey, borderRadius: BorderRadius.circular(12)),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Expanded(
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(report['title'] ?? 'No Title', style: const TextStyle(fontWeight: FontWeight.bold)),
                const SizedBox(height: 5),
                Text(report['status']?.toUpperCase() ?? 'PENDING',
                    style: TextStyle(fontSize: 10, color: _countyGreen, fontWeight: FontWeight.bold)),
              ]),
            ),
            const Icon(Icons.chevron_right, color: Colors.grey),
          ],
        ),
      ),
    );
  }

  Widget _buildActionCard(String title, IconData icon, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(15),
        decoration: BoxDecoration(color: _cardGrey, borderRadius: BorderRadius.circular(12)),
        child: Column(children: [
          Icon(icon, color: _countyGreen),
          const SizedBox(height: 10),
          Text(title, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold), textAlign: TextAlign.center),
        ]),
      ),
    );
  }
}