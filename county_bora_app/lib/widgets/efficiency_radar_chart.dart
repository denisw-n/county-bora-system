import 'package:flutter/material.dart';
import 'package:flutter_radar_chart/flutter_radar_chart.dart';

class EfficiencyRadarChart extends StatelessWidget {
  final List<String> labels;
  final List<double> values;
  final Function(int) onTap;

  const EfficiencyRadarChart({
    super.key,
    required this.labels,
    required this.values,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final data = [values];

    return Container(
      height: 300,
      padding: const EdgeInsets.all(16),
      // We wrap the chart in a GestureDetector to detect taps
      child: GestureDetector(
        onTapDown: (TapDownDetails details) {
          // Get the local position of the tap
          final RenderBox box = context.findRenderObject() as RenderBox;
          final localPosition = box.globalToLocal(details.globalPosition);

          // Calculate the angle of the tap relative to the center
          final center = Offset(box.size.width / 2, box.size.height / 2);
          final delta = localPosition - center;
          final angle = (delta.direction + (3.14159 / 2)) % (2 * 3.14159);

          // Determine which slice (index) was tapped
          final sliceCount = labels.length;
          final sliceAngle = (2 * 3.14159) / sliceCount;
          final tappedIndex = (angle / sliceAngle).floor() % sliceCount;

          // Trigger the callback
          onTap(tappedIndex);
        },
        child: RadarChart(
          ticks: const [20, 40, 60, 80, 100],
          features: labels,
          data: data,
          graphColors: [const Color(0xFF00872E)],
          outlineColor: Colors.grey.shade300,
          axisColor: Colors.grey.shade300,
        ),
      ),
    );
  }
}