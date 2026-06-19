import 'dart:async';
import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import '../services/api_service.dart';
import '../widgets/trend_chart.dart';
import '../widgets/efficiency_radar_chart.dart';
import 'package:county_bora_app/widgets/app_refresh_indicator.dart';

class TransparencyScreen extends StatefulWidget {
  const TransparencyScreen({super.key});

  @override
  State<TransparencyScreen> createState() => _TransparencyScreenState();
}

class _TransparencyScreenState extends State<TransparencyScreen> {
  final ApiService _apiService = ApiService();

  Map<String, dynamic>? _statsData;
  bool _isLoading = true;

  int touchedIndex = -1;
  int radarTouchedIndex = -1;
  int barTouchedIndex = -1;
  Timer? _timer;

  final Color colorResolved = const Color(0xFF00872E);
  final Color colorPending = const Color(0xFFFFC107);
  final Color colorInProgress = const Color(0xFF2196F3);
  final Color colorDispatched = const Color(0xFF9C27B0);

  @override
  void initState() {
    super.initState();
    _loadStats();
  }

  Future<void> _loadStats() async {
    setState(() => _isLoading = true);
    try {
      final data = await _apiService.getTransparencyStats();
      if (mounted) {
        setState(() {
          _statsData = data;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  void _handleTouch(int index, bool isRadar, {bool isBar = false}) {
    setState(() {
      if (isBar) {
        barTouchedIndex = index;
      } else if (isRadar) {
        radarTouchedIndex = index;
      } else {
        touchedIndex = index;
      }
      _timer?.cancel();
      _timer = Timer(const Duration(seconds: 15), () {
        if (mounted) setState(() {
          touchedIndex = -1;
          radarTouchedIndex = -1;
          barTouchedIndex = -1;
        });
      });
    });
  }

  Widget _buildSectionHeader(String title, String description) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(title, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
        const SizedBox(height: 4),
        Text(description, style: TextStyle(fontSize: 12, color: Colors.grey[700])),
      ],
    );
  }

  PieChartSectionData _buildSection(int index, String title, dynamic value, Color color) {
    final bool isTouched = index == touchedIndex;
    final int val = (value is int) ? value : (double.tryParse(value.toString())?.toInt() ?? 0);
    return PieChartSectionData(
      value: val.toDouble(),
      color: color,
      title: isTouched ? '$title\n$val' : '',
      radius: isTouched ? 55.0 : 45.0,
      titleStyle: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.white),
    );
  }

  @override
  Widget build(BuildContext context) {
    final data = _statsData;

    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: AppBar(
        title: const Text("Transparency Portal", style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: const Color(0xFF008444),
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : data == null
          ? const Center(child: Text("Data unavailable"))
          : AppRefreshIndicator(
        onRefresh: _loadStats,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildOverviewSection(data),
              const SizedBox(height: 20),
              _buildTrendsSection(data),
              const SizedBox(height: 20),
              _buildPerformanceSection(data),
              const SizedBox(height: 20),
              _buildEfficiencySection(data),
              const SizedBox(height: 20),
              _buildDistributionSection(data),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildOverviewSection(Map<String, dynamic> data) {
    final status = data['status'] as Map<String, dynamic>? ?? {};
    final total = data['total_issues'] ?? 0;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionHeader("Overview", "High-level summary of all active issues."),
        const SizedBox(height: 10),
        SizedBox(height: 100, child: ListView(scrollDirection: Axis.horizontal, children: [
          _MetricCard(title: "Total", value: "$total", color: Colors.indigo),
          const SizedBox(width: 12),
          _MetricCard(title: "Resolved", value: "${status['Resolved'] ?? 0}", color: colorResolved),
          const SizedBox(width: 12),
          _MetricCard(title: "Pending", value: "${status['Pending'] ?? 0}", color: colorPending),
        ])),
      ],
    );
  }

  Widget _buildTrendsSection(Map<String, dynamic> data) {
    final trendLabels = (data['trendLabels'] as List?) ?? [];
    final trendValues = (data['trends'] as List?) ?? [];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionHeader("Historical Trends", "Performance trajectory over time."),
        const Padding(padding: EdgeInsets.only(top: 4, bottom: 10), child: Text("Visualizing issue resolution progress over the recent cycles.", style: TextStyle(fontSize: 12, color: Colors.grey))),
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15)),
          child: TrendChart(labels: trendLabels, values: trendValues),
        ),
      ],
    );
  }

  Widget _buildPerformanceSection(Map<String, dynamic> data) {
    final barLabels = (data['labels'] as List?)?.map((e) => e.toString()).toList() ?? [];
    final barValues = (data['performance'] as List?)?.map((e) => double.tryParse(e.toString()) ?? 0.0).toList() ?? [];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionHeader("Department Performance", "Active scores per department."),
        const Padding(padding: EdgeInsets.only(top: 4, bottom: 10), child: Text("Tap a bar to reveal the specific performance percentage for that department.", style: TextStyle(fontSize: 12, color: Colors.grey))),
        Container(
          height: 300, padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15)),
          child: Column(
            children: [
              Expanded(
                child: BarChart(BarChartData(
                  maxY: 100,
                  barTouchData: BarTouchData(touchCallback: (event, response) {
                    if (response?.spot != null) _handleTouch(response!.spot!.touchedBarGroupIndex, false, isBar: true);
                  }),
                  titlesData: FlTitlesData(
                    bottomTitles: AxisTitles(sideTitles: SideTitles(showTitles: true, reservedSize: 80, getTitlesWidget: (v, m) => Padding(padding: const EdgeInsets.only(top: 10), child: Transform.rotate(angle: -0.5, child: Text(v.toInt() < barLabels.length ? barLabels[v.toInt()] : "", style: const TextStyle(fontSize: 9, fontWeight: FontWeight.bold)))))),
                    leftTitles: AxisTitles(sideTitles: SideTitles(showTitles: true, interval: 20, reservedSize: 30)),
                  ),
                  barGroups: barValues.asMap().entries.map((e) => BarChartGroupData(x: e.key, barRods: [BarChartRodData(toY: e.value, color: barTouchedIndex == e.key ? Colors.orange : const Color(0xFF008444), width: 22)])).toList(),
                )),
              ),
              if (barTouchedIndex != -1) Padding(padding: const EdgeInsets.only(top: 10), child: Text("${barLabels[barTouchedIndex]}: ${barValues[barTouchedIndex].toStringAsFixed(1)}%", style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.orange))),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildEfficiencySection(Map<String, dynamic> data) {
    final radarLabels = (data['radarLabels'] as List?)?.map((e) => e.toString()).toList() ?? [];
    final radarValues = (data['radarData'] as List?)?.map((e) => double.tryParse(e.toString()) ?? 0.0).toList() ?? [];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionHeader("Dept. Efficiency Index", "Efficiency scores by sector."),
        const Padding(padding: EdgeInsets.only(top: 4, bottom: 10), child: Text("Tap on the radar nodes to view individual sector efficiency index.", style: TextStyle(fontSize: 12, color: Colors.grey))),
        Container(
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15)),
          padding: const EdgeInsets.all(16),
          child: Stack(alignment: Alignment.center, children: [
            EfficiencyRadarChart(labels: radarLabels, values: radarValues, onTap: (index) => _handleTouch(index, true)),
            if (radarTouchedIndex != -1) Container(padding: const EdgeInsets.all(8), decoration: BoxDecoration(color: Colors.black87, borderRadius: BorderRadius.circular(4)), child: Text("${radarLabels[radarTouchedIndex]}: ${radarValues[radarTouchedIndex].toInt()}", style: const TextStyle(color: Colors.white, fontSize: 12))),
          ]),
        ),
      ],
    );
  }

  Widget _buildDistributionSection(Map<String, dynamic> data) {
    final status = data['status'] as Map<String, dynamic>? ?? {};
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionHeader("Status Distribution", "Breakdown by lifecycle."),
        const Padding(padding: EdgeInsets.only(top: 4, bottom: 10), child: Text("Tap a pie segment to see the volume of issues per status category.", style: TextStyle(fontSize: 12, color: Colors.grey))),
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15)),
          child: SizedBox(height: 200, child: PieChart(PieChartData(
            pieTouchData: PieTouchData(touchCallback: (event, response) {
              if (response?.touchedSection != null) _handleTouch(response!.touchedSection!.touchedSectionIndex, false);
            }),
            sections: [
              _buildSection(0, "Resolved", status['Resolved'] ?? 0, colorResolved),
              _buildSection(1, "Pending", status['Pending'] ?? 0, colorPending),
              _buildSection(2, "In-progress", status['In-progress'] ?? 0, colorInProgress),
              _buildSection(3, "Dispatched", status['Dispatched'] ?? 0, colorDispatched),
            ],
          ))),
        ),
      ],
    );
  }
}

class _MetricCard extends StatelessWidget {
  final String title; final String value; final Color color;
  const _MetricCard({required this.title, required this.value, required this.color});
  @override
  Widget build(BuildContext context) {
    return Container(width: 140, padding: const EdgeInsets.all(16), decoration: BoxDecoration(gradient: LinearGradient(colors: [color.withOpacity(0.9), color], begin: Alignment.topLeft, end: Alignment.bottomRight), borderRadius: BorderRadius.circular(20), boxShadow: [BoxShadow(color: color.withOpacity(0.3), blurRadius: 8, offset: const Offset(0, 4))]), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(title.toUpperCase(), style: TextStyle(color: Colors.white.withOpacity(0.8), fontSize: 10, fontWeight: FontWeight.w800, letterSpacing: 1.2)), const SizedBox(height: 8), Text(value, style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w900))]));
  }
}