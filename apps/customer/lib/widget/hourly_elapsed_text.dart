import 'dart:async';

import 'package:arrow_shared/hourly_service_billing.dart';
import 'package:flutter/material.dart';

class HourlyElapsedText extends StatefulWidget {
  const HourlyElapsedText({
    super.key,
    required this.start,
    this.end,
    this.style,
    this.prefix = '',
  });

  final DateTime start;
  final DateTime? end;
  final TextStyle? style;
  final String prefix;

  @override
  State<HourlyElapsedText> createState() => _HourlyElapsedTextState();
}

class _HourlyElapsedTextState extends State<HourlyElapsedText> {
  Timer? _tick;

  @override
  void initState() {
    super.initState();
    if (widget.end == null) {
      _tick = Timer.periodic(const Duration(seconds: 1), (_) {
        if (mounted) setState(() {});
      });
    }
  }

  @override
  void didUpdateWidget(HourlyElapsedText oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.end != null) {
      _tick?.cancel();
      _tick = null;
    } else if (_tick == null) {
      _tick = Timer.periodic(const Duration(seconds: 1), (_) {
        if (mounted) setState(() {});
      });
    }
  }

  @override
  void dispose() {
    _tick?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final end = widget.end ?? DateTime.now();
    return Text(
      '${widget.prefix}${HourlyServiceBilling.formatElapsed(end.difference(widget.start))}',
      style: widget.style,
    );
  }
}
