/// Currencies and countries the app knows about. A country picks a default
/// currency, tax name, phone code and financial-year start.
class CurrencyInfo {
  const CurrencyInfo({
    required this.code,
    required this.symbol,
    required this.name,
    required this.major,
    required this.minor,
    this.indianGrouping = false,
  });

  final String code;

  /// Shown before amounts. Must exist in the bundled Noto Sans font so it
  /// also prints in PDFs.
  final String symbol;
  final String name;

  /// Plural names used when an amount is written in words.
  final String major;
  final String minor;

  /// 12,34,567 (lakh / crore) instead of 1,234,567.
  final bool indianGrouping;

  /// Letters-only symbols such as "AED" or "Rs" need a space: "AED 500".
  bool get spaced => RegExp(r'[A-Za-z]$').hasMatch(symbol);
}

class Currencies {
  Currencies._();

  static const inr = CurrencyInfo(
      code: 'INR', symbol: '₹', name: 'Indian Rupee', major: 'Rupees', minor: 'Paise', indianGrouping: true);

  static const all = <CurrencyInfo>[
    inr,
    CurrencyInfo(code: 'USD', symbol: r'$', name: 'US Dollar', major: 'US Dollars', minor: 'Cents'),
    CurrencyInfo(code: 'GBP', symbol: '£', name: 'British Pound', major: 'Pounds', minor: 'Pence'),
    CurrencyInfo(code: 'EUR', symbol: '€', name: 'Euro', major: 'Euros', minor: 'Cents'),
    CurrencyInfo(code: 'AED', symbol: 'AED', name: 'UAE Dirham', major: 'Dirhams', minor: 'Fils'),
    CurrencyInfo(code: 'SAR', symbol: 'SAR', name: 'Saudi Riyal', major: 'Riyals', minor: 'Halalas'),
    CurrencyInfo(code: 'QAR', symbol: 'QAR', name: 'Qatari Riyal', major: 'Riyals', minor: 'Dirhams'),
    CurrencyInfo(code: 'OMR', symbol: 'OMR', name: 'Omani Rial', major: 'Rials', minor: 'Baisa'),
    CurrencyInfo(
        code: 'NPR', symbol: 'Rs', name: 'Nepalese Rupee', major: 'Rupees', minor: 'Paisa', indianGrouping: true),
    CurrencyInfo(
        code: 'BDT', symbol: 'Tk', name: 'Bangladeshi Taka', major: 'Taka', minor: 'Poisha', indianGrouping: true),
    CurrencyInfo(
        code: 'LKR', symbol: 'Rs', name: 'Sri Lankan Rupee', major: 'Rupees', minor: 'Cents', indianGrouping: true),
    CurrencyInfo(
        code: 'PKR', symbol: 'Rs', name: 'Pakistani Rupee', major: 'Rupees', minor: 'Paisa', indianGrouping: true),
    CurrencyInfo(code: 'SGD', symbol: r'S$', name: 'Singapore Dollar', major: 'Singapore Dollars', minor: 'Cents'),
    CurrencyInfo(code: 'MYR', symbol: 'RM', name: 'Malaysian Ringgit', major: 'Ringgit', minor: 'Sen'),
    CurrencyInfo(code: 'IDR', symbol: 'Rp', name: 'Indonesian Rupiah', major: 'Rupiah', minor: 'Sen'),
    CurrencyInfo(code: 'PHP', symbol: '₱', name: 'Philippine Peso', major: 'Pesos', minor: 'Centavos'),
    CurrencyInfo(code: 'THB', symbol: 'THB', name: 'Thai Baht', major: 'Baht', minor: 'Satang'),
    CurrencyInfo(code: 'AUD', symbol: r'A$', name: 'Australian Dollar', major: 'Australian Dollars', minor: 'Cents'),
    CurrencyInfo(code: 'NZD', symbol: r'NZ$', name: 'New Zealand Dollar', major: 'NZ Dollars', minor: 'Cents'),
    CurrencyInfo(code: 'CAD', symbol: r'C$', name: 'Canadian Dollar', major: 'Canadian Dollars', minor: 'Cents'),
    CurrencyInfo(code: 'ZAR', symbol: 'R', name: 'South African Rand', major: 'Rand', minor: 'Cents'),
    CurrencyInfo(code: 'NGN', symbol: '₦', name: 'Nigerian Naira', major: 'Naira', minor: 'Kobo'),
    CurrencyInfo(code: 'KES', symbol: 'KSh', name: 'Kenyan Shilling', major: 'Shillings', minor: 'Cents'),
    CurrencyInfo(code: 'GHS', symbol: '₵', name: 'Ghanaian Cedi', major: 'Cedis', minor: 'Pesewas'),
    CurrencyInfo(code: 'EGP', symbol: 'EGP', name: 'Egyptian Pound', major: 'Pounds', minor: 'Piastres'),
    CurrencyInfo(code: 'TRY', symbol: '₺', name: 'Turkish Lira', major: 'Lira', minor: 'Kurus'),
  ];

  static CurrencyInfo byCode(String? code) =>
      all.firstWhere((c) => c.code == code, orElse: () => inr);
}

class Country {
  const Country({
    required this.code,
    required this.name,
    required this.dial,
    required this.currency,
    this.taxLabel = 'VAT',
    this.taxRate = 0,
    this.fiscalStartMonth = 1,
    this.bankCodeLabel = 'SWIFT / BIC',
    this.hasUpi = false,
  });

  final String code;
  final String name;
  final String dial;
  final String currency;
  final String taxLabel;
  final double taxRate;

  /// Month the financial year starts (April for India and the UK).
  final int fiscalStartMonth;
  final String bankCodeLabel;
  final bool hasUpi;
}

class Countries {
  Countries._();

  static const india = Country(
    code: 'IN',
    name: 'India',
    dial: '+91',
    currency: 'INR',
    taxLabel: 'GST',
    taxRate: 18,
    fiscalStartMonth: 4,
    bankCodeLabel: 'IFSC',
    hasUpi: true,
  );

  static const all = <Country>[
    india,
    Country(code: 'US', name: 'United States', dial: '+1', currency: 'USD', taxLabel: 'Sales tax', bankCodeLabel: 'Routing number'),
    Country(code: 'GB', name: 'United Kingdom', dial: '+44', currency: 'GBP', taxLabel: 'VAT', taxRate: 20, fiscalStartMonth: 4, bankCodeLabel: 'Sort code'),
    Country(code: 'AE', name: 'United Arab Emirates', dial: '+971', currency: 'AED', taxLabel: 'VAT', taxRate: 5),
    Country(code: 'SA', name: 'Saudi Arabia', dial: '+966', currency: 'SAR', taxLabel: 'VAT', taxRate: 15),
    Country(code: 'QA', name: 'Qatar', dial: '+974', currency: 'QAR'),
    Country(code: 'OM', name: 'Oman', dial: '+968', currency: 'OMR', taxLabel: 'VAT', taxRate: 5),
    Country(code: 'NP', name: 'Nepal', dial: '+977', currency: 'NPR', taxLabel: 'VAT', taxRate: 13, fiscalStartMonth: 4),
    Country(code: 'BD', name: 'Bangladesh', dial: '+880', currency: 'BDT', taxLabel: 'VAT', taxRate: 15, fiscalStartMonth: 7),
    Country(code: 'LK', name: 'Sri Lanka', dial: '+94', currency: 'LKR', taxLabel: 'VAT', taxRate: 18, fiscalStartMonth: 4),
    Country(code: 'PK', name: 'Pakistan', dial: '+92', currency: 'PKR', taxLabel: 'GST', taxRate: 18, fiscalStartMonth: 7),
    Country(code: 'SG', name: 'Singapore', dial: '+65', currency: 'SGD', taxLabel: 'GST', taxRate: 9),
    Country(code: 'MY', name: 'Malaysia', dial: '+60', currency: 'MYR', taxLabel: 'SST', taxRate: 8),
    Country(code: 'ID', name: 'Indonesia', dial: '+62', currency: 'IDR', taxLabel: 'VAT', taxRate: 11),
    Country(code: 'PH', name: 'Philippines', dial: '+63', currency: 'PHP', taxLabel: 'VAT', taxRate: 12),
    Country(code: 'TH', name: 'Thailand', dial: '+66', currency: 'THB', taxLabel: 'VAT', taxRate: 7),
    Country(code: 'AU', name: 'Australia', dial: '+61', currency: 'AUD', taxLabel: 'GST', taxRate: 10, fiscalStartMonth: 7, bankCodeLabel: 'BSB'),
    Country(code: 'NZ', name: 'New Zealand', dial: '+64', currency: 'NZD', taxLabel: 'GST', taxRate: 15, fiscalStartMonth: 4),
    Country(code: 'CA', name: 'Canada', dial: '+1', currency: 'CAD', taxLabel: 'GST/HST', taxRate: 5, fiscalStartMonth: 4),
    Country(code: 'DE', name: 'Germany', dial: '+49', currency: 'EUR', taxLabel: 'VAT', taxRate: 19, bankCodeLabel: 'IBAN'),
    Country(code: 'FR', name: 'France', dial: '+33', currency: 'EUR', taxLabel: 'VAT', taxRate: 20, bankCodeLabel: 'IBAN'),
    Country(code: 'IT', name: 'Italy', dial: '+39', currency: 'EUR', taxLabel: 'VAT', taxRate: 22, bankCodeLabel: 'IBAN'),
    Country(code: 'ES', name: 'Spain', dial: '+34', currency: 'EUR', taxLabel: 'VAT', taxRate: 21, bankCodeLabel: 'IBAN'),
    Country(code: 'NL', name: 'Netherlands', dial: '+31', currency: 'EUR', taxLabel: 'VAT', taxRate: 21, bankCodeLabel: 'IBAN'),
    Country(code: 'IE', name: 'Ireland', dial: '+353', currency: 'EUR', taxLabel: 'VAT', taxRate: 23, bankCodeLabel: 'IBAN'),
    Country(code: 'ZA', name: 'South Africa', dial: '+27', currency: 'ZAR', taxLabel: 'VAT', taxRate: 15, fiscalStartMonth: 3),
    Country(code: 'NG', name: 'Nigeria', dial: '+234', currency: 'NGN', taxLabel: 'VAT', taxRate: 7.5),
    Country(code: 'KE', name: 'Kenya', dial: '+254', currency: 'KES', taxLabel: 'VAT', taxRate: 16, fiscalStartMonth: 7),
    Country(code: 'GH', name: 'Ghana', dial: '+233', currency: 'GHS', taxLabel: 'VAT', taxRate: 15),
    Country(code: 'EG', name: 'Egypt', dial: '+20', currency: 'EGP', taxLabel: 'VAT', taxRate: 14, fiscalStartMonth: 7),
    Country(code: 'TR', name: 'Turkey', dial: '+90', currency: 'TRY', taxLabel: 'VAT', taxRate: 20),
  ];

  static Country byCode(String? code) =>
      all.firstWhere((c) => c.code == code, orElse: () => india);
}
