import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';

class TrendChart extends StatelessWidget {
  final List<dynamic> labels;
  final List<dynamic> values;

  const TrendChart({super.key, required this.labels, required this.values});

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 200,
      padding: const EdgeInsets.only(right: 20, top: 20),
      child: LineChart(
        LineChartData(
          gridData: FlGridData(show: true, drawVerticalLine: false),
          titlesData: FlTitlesData(
            leftTitles: AxisTitles(sideTitles: SideTitles(showTitles: true, reservedSize: 40)),
            bottomTitles: AxisTitles(sideTitles: SideTitles(showTitles: true, getTitlesWidget: (value, meta) {
              int index = value.toInt();
              if (index >= 0 && index < labels.length) {
                return Text(labels[index].toString(), style: const TextStyle(fontSize: 10));
              }
              return const Text('');
            })),
            topTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
            rightTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
          ),
          borderData: FlBorderData(show: false),
          lineBarsData: [
            LineChartBarData(
              spots: List.generate(values.length, (i) => FlSpot(i.toDouble(), double.tryParse(values[i].toString()) ?? 0)),
              isCurved: true,
              color: const Color(0xFF008444), // Nairobi County Green
              barWidth: 3,
              belowBarData: BarAreaData(show: true, color: const Color(0xFF008444).withOpacity(0.2)),
            ),
          ],
        ),
      ),
    );
  }
}