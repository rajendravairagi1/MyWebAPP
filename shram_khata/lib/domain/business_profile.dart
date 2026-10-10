import 'dart:convert';

import '../core/countries.dart';

/// The agency's own details. Printed on every statement and invoice.
class BusinessProfile {
  const BusinessProfile({
    this.planId = 'solo',
    this.name = '',
    this.tagline = '',
    this.ownerName = '',
    this.mobile = '',
    this.altMobile = '',
    this.email = '',
    this.website = '',
    this.address = '',
    this.city = '',
    this.state = '',
    this.pincode = '',
    this.logoPath,
    this.taxEnabled = false,
    this.taxLabel = 'GST',
    this.taxId = '',
    this.taxRate = 18,
    this.registrationLabel = 'Registration No.',
    this.registrationId = '',
    this.upiId = '',
    this.upiName = '',
    this.qrImagePath,
    this.bankName = '',
    this.accountName = '',
    this.accountNumber = '',
    this.ifsc = '',
    this.invoicePrefix = 'INV',
    this.paymentTermsDays = 30,
    this.invoiceTerms = '',
    this.footerNote = 'Thank you for your business.',
    this.monthDivisor = 26,
    this.countryCode = 'IN',
    this.currencyCode = 'INR',
    this.setupDone = false,
    this.themeColor = 0xFF0F766E,
  });

  final String planId;
  final String name;
  final String tagline;
  final String ownerName;
  final String mobile;
  final String altMobile;
  final String email;
  final String website;
  final String address;
  final String city;
  final String state;
  final String pincode;
  final String? logoPath;

  /// Tax details are optional: not every agency is GST registered.
  final bool taxEnabled;
  final String taxLabel;
  final String taxId;
  final double taxRate;

  /// Any other registration number (Udyam, labour licence, PAN ...).
  final String registrationLabel;
  final String registrationId;

  final String upiId;
  final String upiName;

  /// Optional uploaded QR image. If absent and [upiId] is set, a UPI QR is
  /// generated for the PDF.
  final String? qrImagePath;
  final String bankName;
  final String accountName;
  final String accountNumber;
  final String ifsc;

  final String invoicePrefix;
  final int paymentTermsDays;
  final String invoiceTerms;
  final String footerNote;

  /// Days used to convert a monthly salary into a per-day rate.
  final int monthDivisor;

  /// Where the business operates. Sets phone code, tax name, bank labels.
  final String countryCode;

  /// Currency shown everywhere (can differ from the country default).
  final String currencyCode;

  /// False until the first-run business setup has been completed or skipped.
  final bool setupDone;

  /// App accent colour as 0xAARRGGBB.
  final int themeColor;

  Country get country => Countries.byCode(countryCode);
  CurrencyInfo get currency => Currencies.byCode(currencyCode);

  bool get isSetUp => name.trim().isNotEmpty;
  bool get hasBank => accountNumber.trim().isNotEmpty;
  bool get hasUpi => upiId.trim().isNotEmpty;

  String get fullAddress => [address, city, state, pincode]
      .where((e) => e.trim().isNotEmpty)
      .join(', ');

  BusinessProfile copyWith({
    String? planId,
    String? name,
    String? tagline,
    String? ownerName,
    String? mobile,
    String? altMobile,
    String? email,
    String? website,
    String? address,
    String? city,
    String? state,
    String? pincode,
    Object? logoPath = _keep,
    bool? taxEnabled,
    String? taxLabel,
    String? taxId,
    double? taxRate,
    String? registrationLabel,
    String? registrationId,
    String? upiId,
    String? upiName,
    Object? qrImagePath = _keep,
    String? bankName,
    String? accountName,
    String? accountNumber,
    String? ifsc,
    String? invoicePrefix,
    int? paymentTermsDays,
    String? invoiceTerms,
    String? footerNote,
    int? monthDivisor,
    String? countryCode,
    String? currencyCode,
    bool? setupDone,
    int? themeColor,
  }) {
    return BusinessProfile(
      planId: planId ?? this.planId,
      name: name ?? this.name,
      tagline: tagline ?? this.tagline,
      ownerName: ownerName ?? this.ownerName,
      mobile: mobile ?? this.mobile,
      altMobile: altMobile ?? this.altMobile,
      email: email ?? this.email,
      website: website ?? this.website,
      address: address ?? this.address,
      city: city ?? this.city,
      state: state ?? this.state,
      pincode: pincode ?? this.pincode,
      logoPath: identical(logoPath, _keep) ? this.logoPath : logoPath as String?,
      taxEnabled: taxEnabled ?? this.taxEnabled,
      taxLabel: taxLabel ?? this.taxLabel,
      taxId: taxId ?? this.taxId,
      taxRate: taxRate ?? this.taxRate,
      registrationLabel: registrationLabel ?? this.registrationLabel,
      registrationId: registrationId ?? this.registrationId,
      upiId: upiId ?? this.upiId,
      upiName: upiName ?? this.upiName,
      qrImagePath:
          identical(qrImagePath, _keep) ? this.qrImagePath : qrImagePath as String?,
      bankName: bankName ?? this.bankName,
      accountName: accountName ?? this.accountName,
      accountNumber: accountNumber ?? this.accountNumber,
      ifsc: ifsc ?? this.ifsc,
      invoicePrefix: invoicePrefix ?? this.invoicePrefix,
      paymentTermsDays: paymentTermsDays ?? this.paymentTermsDays,
      invoiceTerms: invoiceTerms ?? this.invoiceTerms,
      footerNote: footerNote ?? this.footerNote,
      monthDivisor: monthDivisor ?? this.monthDivisor,
      countryCode: countryCode ?? this.countryCode,
      currencyCode: currencyCode ?? this.currencyCode,
      setupDone: setupDone ?? this.setupDone,
      themeColor: themeColor ?? this.themeColor,
    );
  }

  static const _keep = Object();

  Map<String, Object?> toJson() => {
        'planId': planId,
        'name': name,
        'tagline': tagline,
        'ownerName': ownerName,
        'mobile': mobile,
        'altMobile': altMobile,
        'email': email,
        'website': website,
        'address': address,
        'city': city,
        'state': state,
        'pincode': pincode,
        'logoPath': logoPath,
        'taxEnabled': taxEnabled,
        'taxLabel': taxLabel,
        'taxId': taxId,
        'taxRate': taxRate,
        'registrationLabel': registrationLabel,
        'registrationId': registrationId,
        'upiId': upiId,
        'upiName': upiName,
        'qrImagePath': qrImagePath,
        'bankName': bankName,
        'accountName': accountName,
        'accountNumber': accountNumber,
        'ifsc': ifsc,
        'invoicePrefix': invoicePrefix,
        'paymentTermsDays': paymentTermsDays,
        'invoiceTerms': invoiceTerms,
        'footerNote': footerNote,
        'monthDivisor': monthDivisor,
        'countryCode': countryCode,
        'currencyCode': currencyCode,
        'setupDone': setupDone,
        'themeColor': themeColor,
      };

  String encode() => jsonEncode(toJson());

  factory BusinessProfile.decode(String? source) {
    if (source == null || source.isEmpty) return const BusinessProfile();
    final m = jsonDecode(source) as Map<String, dynamic>;
    String s(String k, String d) => (m[k] as String?) ?? d;
    return BusinessProfile(
      planId: s('planId', 'solo'),
      name: s('name', ''),
      tagline: s('tagline', ''),
      ownerName: s('ownerName', ''),
      mobile: s('mobile', ''),
      altMobile: s('altMobile', ''),
      email: s('email', ''),
      website: s('website', ''),
      address: s('address', ''),
      city: s('city', ''),
      state: s('state', ''),
      pincode: s('pincode', ''),
      logoPath: m['logoPath'] as String?,
      taxEnabled: (m['taxEnabled'] as bool?) ?? false,
      taxLabel: s('taxLabel', 'GST'),
      taxId: s('taxId', ''),
      taxRate: (m['taxRate'] as num?)?.toDouble() ?? 18,
      registrationLabel: s('registrationLabel', 'Registration No.'),
      registrationId: s('registrationId', ''),
      upiId: s('upiId', ''),
      upiName: s('upiName', ''),
      qrImagePath: m['qrImagePath'] as String?,
      bankName: s('bankName', ''),
      accountName: s('accountName', ''),
      accountNumber: s('accountNumber', ''),
      ifsc: s('ifsc', ''),
      invoicePrefix: s('invoicePrefix', 'INV'),
      paymentTermsDays: (m['paymentTermsDays'] as num?)?.toInt() ?? 30,
      invoiceTerms: s('invoiceTerms', ''),
      footerNote: s('footerNote', 'Thank you for your business.'),
      monthDivisor: (m['monthDivisor'] as num?)?.toInt() ?? 26,
      countryCode: s('countryCode', 'IN'),
      currencyCode: s('currencyCode', 'INR'),
      setupDone: (m['setupDone'] as bool?) ?? false,
      themeColor: (m['themeColor'] as num?)?.toInt() ?? 0xFF0F766E,
    );
  }
}
