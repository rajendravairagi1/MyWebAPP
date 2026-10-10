import 'dart:io';
import 'dart:typed_data';

import 'package:flutter/material.dart' show Color, Colors, HSLColor;
import 'package:flutter/services.dart' show rootBundle;
import 'package:pdf/pdf.dart';
import 'package:pdf/widgets.dart' as pw;

import '../domain/business_profile.dart';

/// Shared look for every PDF: fonts (with Devanagari fallback and the ₹
/// glyph), the brand palette, letterhead and footer.
class PdfKit {
  PdfKit._();

  static PdfColor brand = PdfColor.fromInt(0xFF0F766E);
  static PdfColor brandDark = PdfColor.fromInt(0xFF0B4F4A);
  static PdfColor brandTint = PdfColor.fromInt(0xFFE7F3F1);
  /// Re-derives the brand colours from the colour chosen in Business profile.
  static void applyBrand(int argb) {
    final c = PdfColor.fromInt(argb);
    brand = c;
    final hsl = HSLColor.fromColor(Color(argb));
    brandDark = PdfColor.fromInt(
        hsl.withLightness((hsl.lightness * 0.62).clamp(0.08, 0.5)).toColor().toARGB32());
    brandTint = PdfColor.fromInt(Color.lerp(Colors.white, Color(argb), 0.10)!.toARGB32());
  }

  static final ink = PdfColor.fromInt(0xFF111827);
  static final muted = PdfColor.fromInt(0xFF6B7280);
  static final line = PdfColor.fromInt(0xFFE5E7EB);
  static final zebra = PdfColor.fromInt(0xFFF8FAFB);

  static final present = PdfColor.fromInt(0xFF15803D);
  static final presentBg = PdfColor.fromInt(0xFFDCFCE7);
  static final half = PdfColor.fromInt(0xFFB45309);
  static final halfBg = PdfColor.fromInt(0xFFFEF3C7);
  static final absent = PdfColor.fromInt(0xFFB91C1C);
  static final absentBg = PdfColor.fromInt(0xFFFEE2E2);

  static pw.ThemeData? _theme;

  static Future<pw.Font> _font(String asset) async =>
      pw.Font.ttf(await rootBundle.load(asset));

  static Future<pw.ThemeData> theme() async {
    if (_theme != null) return _theme!;
    final regular = await _font('assets/fonts/NotoSans_400Regular.ttf');
    final bold = await _font('assets/fonts/NotoSans_700Bold.ttf');
    final dRegular = await _font('assets/fonts/NotoSansDevanagari_400Regular.ttf');
    final dBold = await _font('assets/fonts/NotoSansDevanagari_700Bold.ttf');
    _theme = pw.ThemeData.withFont(
      base: regular,
      bold: bold,
      italic: regular,
      boldItalic: bold,
      fontFallback: [dRegular, dBold],
    );
    return _theme!;
  }

  static pw.TextStyle t(
    double size, {
    bool bold = false,
    PdfColor? color,
    double? spacing,
  }) =>
      pw.TextStyle(
        fontSize: size,
        fontWeight: bold ? pw.FontWeight.bold : pw.FontWeight.normal,
        color: color ?? ink,
        letterSpacing: spacing,
      );

  /// Letterhead: logo, name, address, tax ids and contact on the left, the
  /// document title and key facts on the right.
  static pw.Widget letterhead(
    BusinessProfile p,
    PdfAssets assets, {
    required String title,
    List<(String, String)> meta = const [],
    String? branchLine,
  }) {
    final ids = <String>[
      if (p.taxEnabled && p.taxId.trim().isNotEmpty) '${p.taxLabel}: ${p.taxId.trim()}',
      if (p.registrationId.trim().isNotEmpty) '${p.registrationLabel}: ${p.registrationId.trim()}',
    ];
    final contact = <String>[
      if (p.mobile.trim().isNotEmpty) p.mobile.trim(),
      if (p.email.trim().isNotEmpty) p.email.trim(),
      if (p.website.trim().isNotEmpty) p.website.trim(),
    ];
    return pw.Column(
      crossAxisAlignment: pw.CrossAxisAlignment.start,
      children: [
        pw.Row(
          crossAxisAlignment: pw.CrossAxisAlignment.start,
          children: [
            if (assets.logo != null) ...[
              pw.Container(
                width: 54,
                height: 54,
                decoration: pw.BoxDecoration(
                  borderRadius: pw.BorderRadius.circular(6),
                  border: pw.Border.all(color: line, width: 0.8),
                ),
                padding: const pw.EdgeInsets.all(3),
                child: pw.Image(pw.MemoryImage(assets.logo!), fit: pw.BoxFit.contain),
              ),
              pw.SizedBox(width: 12),
            ],
            pw.Expanded(
              flex: 3,
              child: pw.Column(
                crossAxisAlignment: pw.CrossAxisAlignment.start,
                children: [
                  pw.Text(p.name.isEmpty ? 'Your Agency' : p.name,
                      style: t(17, bold: true, color: brandDark)),
                  if (p.tagline.trim().isNotEmpty)
                    pw.Text(p.tagline.trim(), style: t(8.5, color: muted)),
                  if (branchLine != null && branchLine.isNotEmpty)
                    pw.Text(branchLine, style: t(8.5, color: muted)),
                  if (p.fullAddress.isNotEmpty) ...[
                    pw.SizedBox(height: 2),
                    pw.Text(p.fullAddress, style: t(8.5)),
                  ],
                  if (ids.isNotEmpty)
                    pw.Padding(
                      padding: const pw.EdgeInsets.only(top: 2),
                      child: pw.Text(ids.join('    '), style: t(8.5, bold: true)),
                    ),
                  if (contact.isNotEmpty)
                    pw.Padding(
                      padding: const pw.EdgeInsets.only(top: 2),
                      child: pw.Text(contact.join('   •   '), style: t(8.5, color: muted)),
                    ),
                ],
              ),
            ),
            pw.SizedBox(width: 12),
            pw.Expanded(
              flex: 2,
              child: pw.Column(
                crossAxisAlignment: pw.CrossAxisAlignment.end,
                children: [
                  pw.Text(title, style: t(15, bold: true, color: brand, spacing: 1.2)),
                  pw.SizedBox(height: 4),
                  for (final m in meta)
                    pw.Padding(
                      padding: const pw.EdgeInsets.only(top: 1.5),
                      child: pw.RichText(
                        text: pw.TextSpan(children: [
                          pw.TextSpan(text: '${m.$1}  ', style: t(8.5, color: muted)),
                          pw.TextSpan(text: m.$2, style: t(9, bold: true)),
                        ]),
                      ),
                    ),
                ],
              ),
            ),
          ],
        ),
        pw.SizedBox(height: 10),
        pw.Container(height: 2.2, color: brand),
      ],
    );
  }

  /// Page footer: payment QR, thank-you note, contact details, page number.
  static pw.Widget footer(
    pw.Context ctx,
    BusinessProfile p,
    PdfAssets assets, {
    pw.Widget? qr,
  }) {
    final contact = <String>[
      if (p.mobile.trim().isNotEmpty) 'Tel: ${p.mobile.trim()}',
      if (p.email.trim().isNotEmpty) p.email.trim(),
      if (p.website.trim().isNotEmpty) p.website.trim(),
    ];
    return pw.Container(
      margin: const pw.EdgeInsets.only(top: 8),
      padding: const pw.EdgeInsets.only(top: 6),
      decoration: pw.BoxDecoration(border: pw.Border(top: pw.BorderSide(color: line, width: 0.8))),
      child: pw.Row(
        crossAxisAlignment: pw.CrossAxisAlignment.center,
        children: [
          pw.Expanded(
            child: pw.Column(
              crossAxisAlignment: pw.CrossAxisAlignment.start,
              children: [
                if (p.footerNote.trim().isNotEmpty)
                  pw.Text(p.footerNote.trim(), style: t(8.5, bold: true, color: brandDark)),
                if (contact.isNotEmpty)
                  pw.Text(contact.join('   •   '), style: t(8, color: muted)),
                if (p.name.isNotEmpty)
                  pw.Text(p.name, style: t(8, color: muted)),
              ],
            ),
          ),
          if (qr != null) ...[pw.SizedBox(width: 10), qr],
          pw.SizedBox(width: 12),
          pw.Text('Page ${ctx.pageNumber} of ${ctx.pagesCount}', style: t(8, color: muted)),
        ],
      ),
    );
  }

  /// The payment QR block: an uploaded QR image wins, otherwise a UPI QR is
  /// generated from the UPI id (with the amount when [amountPaise] is given).
  static pw.Widget? qrBlock(
    BusinessProfile p,
    PdfAssets assets, {
    int? amountPaise,
    String? note,
    double size = 78,
    bool compact = false,
  }) {
    pw.Widget? code;
    if (assets.qr != null) {
      code = pw.Image(pw.MemoryImage(assets.qr!), width: size, height: size, fit: pw.BoxFit.contain);
    } else if (p.hasUpi) {
      code = pw.BarcodeWidget(
        barcode: pw.Barcode.qrCode(),
        data: upiLink(p, amountPaise: amountPaise, note: note),
        width: size,
        height: size,
        drawText: false,
      );
    }
    if (code == null) return null;
    return pw.Column(
      mainAxisSize: pw.MainAxisSize.min,
      children: [
        pw.Container(
          padding: pw.EdgeInsets.all(compact ? 2 : 4),
          decoration: pw.BoxDecoration(
            border: pw.Border.all(color: line, width: 0.8),
            borderRadius: pw.BorderRadius.circular(6),
          ),
          child: code,
        ),
        pw.SizedBox(height: compact ? 1.5 : 3),
        pw.Text('Scan to pay', style: t(compact ? 7 : 8, bold: true, color: brandDark)),
        if (p.hasUpi && !compact) pw.Text(p.upiId, style: t(7.5, color: muted)),
      ],
    );
  }

  static String upiLink(BusinessProfile p, {int? amountPaise, String? note}) {
    final name = (p.upiName.trim().isEmpty ? p.name : p.upiName).trim();
    final q = <String, String>{
      'pa': p.upiId.trim(),
      'pn': name,
      'cu': 'INR',
      if (amountPaise != null && amountPaise > 0)
        'am': (amountPaise / 100).toStringAsFixed(2),
      if (note != null && note.isNotEmpty) 'tn': note,
    };
    return 'upi://pay?${q.entries.map((e) => '${e.key}=${Uri.encodeComponent(e.value)}').join('&')}';
  }

  /// A titled rounded box.
  static pw.Widget card(String title, pw.Widget child, {PdfColor? tint}) {
    return pw.Container(
      decoration: pw.BoxDecoration(
        color: tint,
        border: pw.Border.all(color: line, width: 0.8),
        borderRadius: pw.BorderRadius.circular(6),
      ),
      padding: const pw.EdgeInsets.fromLTRB(10, 8, 10, 8),
      child: pw.Column(
        crossAxisAlignment: pw.CrossAxisAlignment.start,
        children: [
          pw.Text(title.toUpperCase(), style: t(7.5, bold: true, color: muted, spacing: 0.8)),
          pw.SizedBox(height: 5),
          child,
        ],
      ),
    );
  }

  /// "Label ........ value" row.
  static pw.Widget kv(String k, String v, {bool bold = false, PdfColor? color, double size = 9}) {
    return pw.Padding(
      padding: const pw.EdgeInsets.symmetric(vertical: 1.8),
      child: pw.Row(
        mainAxisAlignment: pw.MainAxisAlignment.spaceBetween,
        crossAxisAlignment: pw.CrossAxisAlignment.start,
        children: [
          pw.Expanded(child: pw.Text(k, style: t(size, bold: bold, color: bold ? color : muted))),
          pw.SizedBox(width: 8),
          pw.Text(v, style: t(size, bold: bold, color: color)),
        ],
      ),
    );
  }

  static pw.Widget cell(
    String text, {
    pw.Alignment align = pw.Alignment.centerLeft,
    bool bold = false,
    double size = 8.5,
    PdfColor? color,
    pw.EdgeInsets padding = const pw.EdgeInsets.symmetric(horizontal: 6, vertical: 5),
  }) =>
      pw.Container(
        alignment: align,
        padding: padding,
        child: pw.Text(text, style: t(size, bold: bold, color: color)),
      );

  static pw.Widget headCell(String text, {pw.Alignment align = pw.Alignment.centerLeft}) =>
      pw.Container(
        alignment: align,
        padding: const pw.EdgeInsets.symmetric(horizontal: 6, vertical: 6),
        child: pw.Text(text.toUpperCase(),
            style: t(7.5, bold: true, color: PdfColors.white, spacing: 0.6)),
      );

  static PdfColor statusColor(String letter) => switch (letter) {
        'P' => present,
        'H' => half,
        'A' => absent,
        _ => muted,
      };

  static PdfColor statusBg(String letter) => switch (letter) {
        'P' => presentBg,
        'H' => halfBg,
        'A' => absentBg,
        _ => PdfColors.white,
      };
}

/// Images loaded from disk for the PDFs (logo and uploaded payment QR).
class PdfAssets {
  PdfAssets({this.logo, this.qr});
  final Uint8List? logo;
  final Uint8List? qr;

  static Future<PdfAssets> load(BusinessProfile p) async {
    PdfKit.applyBrand(p.themeColor);
    Future<Uint8List?> read(String? path) async {
      if (path == null || path.isEmpty) return null;
      try {
        final f = File(path);
        if (await f.exists()) return await f.readAsBytes();
      } catch (_) {}
      return null;
    }

    return PdfAssets(logo: await read(p.logoPath), qr: await read(p.qrImagePath));
  }
}
