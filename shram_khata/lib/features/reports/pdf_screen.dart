import 'dart:typed_data';

import 'package:flutter/material.dart';
import 'package:printing/printing.dart';

import '../../core/theme.dart';
import '../../core/widgets.dart';

/// Builds a PDF and shows it with share / print / save actions.
Future<void> openPdf(
  BuildContext context, {
  required String title,
  required String fileName,
  required Future<Uint8List> Function() build,
}) {
  return Navigator.push(
    context,
    MaterialPageRoute(builder: (_) => PdfScreen(title: title, fileName: fileName, build: build)),
  );
}

class PdfScreen extends StatefulWidget {
  const PdfScreen({super.key, required this.title, required this.fileName, required this.build});
  final String title;
  final String fileName;
  final Future<Uint8List> Function() build;

  @override
  State<PdfScreen> createState() => _PdfScreenState();
}

class _PdfScreenState extends State<PdfScreen> {
  late final Future<Uint8List> _bytes = widget.build();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(widget.title)),
      body: FutureBuilder<Uint8List>(
        future: _bytes,
        builder: (context, snap) {
          if (snap.hasError) return ErrorView(snap.error!);
          if (!snap.hasData) return const LoadingView();
          return PdfPreview(
            build: (_) => snap.data!,
            pdfFileName: '${widget.fileName}.pdf',
            canChangeOrientation: false,
            canChangePageFormat: false,
            canDebug: false,
            maxPageWidth: 700,
            scrollViewDecoration: const BoxDecoration(color: Palette.bg),
            actionBarTheme: const PdfActionBarTheme(
              backgroundColor: Palette.brand,
              iconColor: Colors.white,
            ),
          );
        },
      ),
    );
  }
}
