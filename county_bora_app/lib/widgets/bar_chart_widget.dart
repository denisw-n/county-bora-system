import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';

class BarChartWidget extends StatelessWidget {
  final List<double> values;
  final List<String> titles;

  const BarChartWidget({
    super.key,
    required this.values,
    required this.titles,
  });

  @override
  Widget build(BuildContext context) {
    return BarChart(
      BarChartData(
        alignment: BarChartAlignment.spaceAround,
        maxY: values.reduce((a, b) => a > b ? a : b) + 5,
        barTouchData: BarTouchData(enabled: true),
        titlesData: FlTitlesData(
          bottomTitles: AxisTitles(
            sideTitles: SideTitles(
              showTitles: true,
              getTitlesWidget: (value, meta) => Text(titles[value.toInt()], style: const TextStyle(fontSize: 10)),
            ),
          ),
          leftTitles: AxisTitles(sideTitles: SideTitles(showTitles: true)),
          topTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
          rightTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
        ),
        gridData: FlGridData(show: false),
        borderData: FlBorderData(show: false),
        barGroups: List.generate(values.length, (i) => BarChartGroupData(
          x: i,
          barRods: [
            BarChartRodData(toY: values[i], color: const Color(0xFF008444), width: 20, borderRadius: BorderRadius.circular(4)),
          ],
        )),
      ),
    );
  }
}