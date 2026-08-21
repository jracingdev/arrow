import 'dart:async';

import 'package:arrow_shared/hourly_service_billing.dart';
import 'package:flutter/material.dart';

class ElapsedClock extends StatefulWidget {
  const ElapsedClock({
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
  State<ElapsedClock> createState() => _ElapsedClockState();
}

class _ElapsedClockState extends State<ElapsedClock> {
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
  void didUpdateWidget(ElapsedClock oldWidget) {
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
    final label = HourlyServiceBilling.formatElapsed(end.difference(widget.start));
    return Text('${widget.prefix}$label', style: widget.style);
  }
}
