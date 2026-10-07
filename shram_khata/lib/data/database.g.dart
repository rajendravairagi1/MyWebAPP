// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'database.dart';

// ignore_for_file: type=lint
class $BranchesTable extends Branches with TableInfo<$BranchesTable, Branch> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $BranchesTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _nameMeta = const VerificationMeta('name');
  @override
  late final GeneratedColumn<String> name = GeneratedColumn<String>(
    'name',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _addressMeta = const VerificationMeta(
    'address',
  );
  @override
  late final GeneratedColumn<String> address = GeneratedColumn<String>(
    'address',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _cityMeta = const VerificationMeta('city');
  @override
  late final GeneratedColumn<String> city = GeneratedColumn<String>(
    'city',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _mobileMeta = const VerificationMeta('mobile');
  @override
  late final GeneratedColumn<String> mobile = GeneratedColumn<String>(
    'mobile',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _isActiveMeta = const VerificationMeta(
    'isActive',
  );
  @override
  late final GeneratedColumn<bool> isActive = GeneratedColumn<bool>(
    'is_active',
    aliasedName,
    false,
    type: DriftSqlType.bool,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'CHECK ("is_active" IN (0, 1))',
    ),
    defaultValue: const Constant(true),
  );
  static const VerificationMeta _createdAtMeta = const VerificationMeta(
    'createdAt',
  );
  @override
  late final GeneratedColumn<DateTime> createdAt = GeneratedColumn<DateTime>(
    'created_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    name,
    address,
    city,
    mobile,
    isActive,
    createdAt,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'branches';
  @override
  VerificationContext validateIntegrity(
    Insertable<Branch> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('name')) {
      context.handle(
        _nameMeta,
        name.isAcceptableOrUnknown(data['name']!, _nameMeta),
      );
    } else if (isInserting) {
      context.missing(_nameMeta);
    }
    if (data.containsKey('address')) {
      context.handle(
        _addressMeta,
        address.isAcceptableOrUnknown(data['address']!, _addressMeta),
      );
    }
    if (data.containsKey('city')) {
      context.handle(
        _cityMeta,
        city.isAcceptableOrUnknown(data['city']!, _cityMeta),
      );
    }
    if (data.containsKey('mobile')) {
      context.handle(
        _mobileMeta,
        mobile.isAcceptableOrUnknown(data['mobile']!, _mobileMeta),
      );
    }
    if (data.containsKey('is_active')) {
      context.handle(
        _isActiveMeta,
        isActive.isAcceptableOrUnknown(data['is_active']!, _isActiveMeta),
      );
    }
    if (data.containsKey('created_at')) {
      context.handle(
        _createdAtMeta,
        createdAt.isAcceptableOrUnknown(data['created_at']!, _createdAtMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  Branch map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return Branch(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      name: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}name'],
      )!,
      address: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}address'],
      )!,
      city: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}city'],
      )!,
      mobile: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}mobile'],
      )!,
      isActive: attachedDatabase.typeMapping.read(
        DriftSqlType.bool,
        data['${effectivePrefix}is_active'],
      )!,
      createdAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}created_at'],
      )!,
    );
  }

  @override
  $BranchesTable createAlias(String alias) {
    return $BranchesTable(attachedDatabase, alias);
  }
}

class Branch extends DataClass implements Insertable<Branch> {
  final String id;
  final String name;
  final String address;
  final String city;
  final String mobile;
  final bool isActive;
  final DateTime createdAt;
  const Branch({
    required this.id,
    required this.name,
    required this.address,
    required this.city,
    required this.mobile,
    required this.isActive,
    required this.createdAt,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['name'] = Variable<String>(name);
    map['address'] = Variable<String>(address);
    map['city'] = Variable<String>(city);
    map['mobile'] = Variable<String>(mobile);
    map['is_active'] = Variable<bool>(isActive);
    map['created_at'] = Variable<DateTime>(createdAt);
    return map;
  }

  BranchesCompanion toCompanion(bool nullToAbsent) {
    return BranchesCompanion(
      id: Value(id),
      name: Value(name),
      address: Value(address),
      city: Value(city),
      mobile: Value(mobile),
      isActive: Value(isActive),
      createdAt: Value(createdAt),
    );
  }

  factory Branch.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return Branch(
      id: serializer.fromJson<String>(json['id']),
      name: serializer.fromJson<String>(json['name']),
      address: serializer.fromJson<String>(json['address']),
      city: serializer.fromJson<String>(json['city']),
      mobile: serializer.fromJson<String>(json['mobile']),
      isActive: serializer.fromJson<bool>(json['isActive']),
      createdAt: serializer.fromJson<DateTime>(json['createdAt']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'name': serializer.toJson<String>(name),
      'address': serializer.toJson<String>(address),
      'city': serializer.toJson<String>(city),
      'mobile': serializer.toJson<String>(mobile),
      'isActive': serializer.toJson<bool>(isActive),
      'createdAt': serializer.toJson<DateTime>(createdAt),
    };
  }

  Branch copyWith({
    String? id,
    String? name,
    String? address,
    String? city,
    String? mobile,
    bool? isActive,
    DateTime? createdAt,
  }) => Branch(
    id: id ?? this.id,
    name: name ?? this.name,
    address: address ?? this.address,
    city: city ?? this.city,
    mobile: mobile ?? this.mobile,
    isActive: isActive ?? this.isActive,
    createdAt: createdAt ?? this.createdAt,
  );
  Branch copyWithCompanion(BranchesCompanion data) {
    return Branch(
      id: data.id.present ? data.id.value : this.id,
      name: data.name.present ? data.name.value : this.name,
      address: data.address.present ? data.address.value : this.address,
      city: data.city.present ? data.city.value : this.city,
      mobile: data.mobile.present ? data.mobile.value : this.mobile,
      isActive: data.isActive.present ? data.isActive.value : this.isActive,
      createdAt: data.createdAt.present ? data.createdAt.value : this.createdAt,
    );
  }

  @override
  String toString() {
    return (StringBuffer('Branch(')
          ..write('id: $id, ')
          ..write('name: $name, ')
          ..write('address: $address, ')
          ..write('city: $city, ')
          ..write('mobile: $mobile, ')
          ..write('isActive: $isActive, ')
          ..write('createdAt: $createdAt')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode =>
      Object.hash(id, name, address, city, mobile, isActive, createdAt);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is Branch &&
          other.id == this.id &&
          other.name == this.name &&
          other.address == this.address &&
          other.city == this.city &&
          other.mobile == this.mobile &&
          other.isActive == this.isActive &&
          other.createdAt == this.createdAt);
}

class BranchesCompanion extends UpdateCompanion<Branch> {
  final Value<String> id;
  final Value<String> name;
  final Value<String> address;
  final Value<String> city;
  final Value<String> mobile;
  final Value<bool> isActive;
  final Value<DateTime> createdAt;
  final Value<int> rowid;
  const BranchesCompanion({
    this.id = const Value.absent(),
    this.name = const Value.absent(),
    this.address = const Value.absent(),
    this.city = const Value.absent(),
    this.mobile = const Value.absent(),
    this.isActive = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  BranchesCompanion.insert({
    required String id,
    required String name,
    this.address = const Value.absent(),
    this.city = const Value.absent(),
    this.mobile = const Value.absent(),
    this.isActive = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       name = Value(name);
  static Insertable<Branch> custom({
    Expression<String>? id,
    Expression<String>? name,
    Expression<String>? address,
    Expression<String>? city,
    Expression<String>? mobile,
    Expression<bool>? isActive,
    Expression<DateTime>? createdAt,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (name != null) 'name': name,
      if (address != null) 'address': address,
      if (city != null) 'city': city,
      if (mobile != null) 'mobile': mobile,
      if (isActive != null) 'is_active': isActive,
      if (createdAt != null) 'created_at': createdAt,
      if (rowid != null) 'rowid': rowid,
    });
  }

  BranchesCompanion copyWith({
    Value<String>? id,
    Value<String>? name,
    Value<String>? address,
    Value<String>? city,
    Value<String>? mobile,
    Value<bool>? isActive,
    Value<DateTime>? createdAt,
    Value<int>? rowid,
  }) {
    return BranchesCompanion(
      id: id ?? this.id,
      name: name ?? this.name,
      address: address ?? this.address,
      city: city ?? this.city,
      mobile: mobile ?? this.mobile,
      isActive: isActive ?? this.isActive,
      createdAt: createdAt ?? this.createdAt,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (name.present) {
      map['name'] = Variable<String>(name.value);
    }
    if (address.present) {
      map['address'] = Variable<String>(address.value);
    }
    if (city.present) {
      map['city'] = Variable<String>(city.value);
    }
    if (mobile.present) {
      map['mobile'] = Variable<String>(mobile.value);
    }
    if (isActive.present) {
      map['is_active'] = Variable<bool>(isActive.value);
    }
    if (createdAt.present) {
      map['created_at'] = Variable<DateTime>(createdAt.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('BranchesCompanion(')
          ..write('id: $id, ')
          ..write('name: $name, ')
          ..write('address: $address, ')
          ..write('city: $city, ')
          ..write('mobile: $mobile, ')
          ..write('isActive: $isActive, ')
          ..write('createdAt: $createdAt, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $RolesTable extends Roles with TableInfo<$RolesTable, Role> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $RolesTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _nameMeta = const VerificationMeta('name');
  @override
  late final GeneratedColumn<String> name = GeneratedColumn<String>(
    'name',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _permissionsMeta = const VerificationMeta(
    'permissions',
  );
  @override
  late final GeneratedColumn<String> permissions = GeneratedColumn<String>(
    'permissions',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _isSystemMeta = const VerificationMeta(
    'isSystem',
  );
  @override
  late final GeneratedColumn<bool> isSystem = GeneratedColumn<bool>(
    'is_system',
    aliasedName,
    false,
    type: DriftSqlType.bool,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'CHECK ("is_system" IN (0, 1))',
    ),
    defaultValue: const Constant(false),
  );
  @override
  List<GeneratedColumn> get $columns => [id, name, permissions, isSystem];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'roles';
  @override
  VerificationContext validateIntegrity(
    Insertable<Role> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('name')) {
      context.handle(
        _nameMeta,
        name.isAcceptableOrUnknown(data['name']!, _nameMeta),
      );
    } else if (isInserting) {
      context.missing(_nameMeta);
    }
    if (data.containsKey('permissions')) {
      context.handle(
        _permissionsMeta,
        permissions.isAcceptableOrUnknown(
          data['permissions']!,
          _permissionsMeta,
        ),
      );
    }
    if (data.containsKey('is_system')) {
      context.handle(
        _isSystemMeta,
        isSystem.isAcceptableOrUnknown(data['is_system']!, _isSystemMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  Role map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return Role(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      name: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}name'],
      )!,
      permissions: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}permissions'],
      )!,
      isSystem: attachedDatabase.typeMapping.read(
        DriftSqlType.bool,
        data['${effectivePrefix}is_system'],
      )!,
    );
  }

  @override
  $RolesTable createAlias(String alias) {
    return $RolesTable(attachedDatabase, alias);
  }
}

class Role extends DataClass implements Insertable<Role> {
  final String id;
  final String name;

  /// Comma separated permission keys (see core/permissions.dart).
  final String permissions;
  final bool isSystem;
  const Role({
    required this.id,
    required this.name,
    required this.permissions,
    required this.isSystem,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['name'] = Variable<String>(name);
    map['permissions'] = Variable<String>(permissions);
    map['is_system'] = Variable<bool>(isSystem);
    return map;
  }

  RolesCompanion toCompanion(bool nullToAbsent) {
    return RolesCompanion(
      id: Value(id),
      name: Value(name),
      permissions: Value(permissions),
      isSystem: Value(isSystem),
    );
  }

  factory Role.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return Role(
      id: serializer.fromJson<String>(json['id']),
      name: serializer.fromJson<String>(json['name']),
      permissions: serializer.fromJson<String>(json['permissions']),
      isSystem: serializer.fromJson<bool>(json['isSystem']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'name': serializer.toJson<String>(name),
      'permissions': serializer.toJson<String>(permissions),
      'isSystem': serializer.toJson<bool>(isSystem),
    };
  }

  Role copyWith({
    String? id,
    String? name,
    String? permissions,
    bool? isSystem,
  }) => Role(
    id: id ?? this.id,
    name: name ?? this.name,
    permissions: permissions ?? this.permissions,
    isSystem: isSystem ?? this.isSystem,
  );
  Role copyWithCompanion(RolesCompanion data) {
    return Role(
      id: data.id.present ? data.id.value : this.id,
      name: data.name.present ? data.name.value : this.name,
      permissions: data.permissions.present
          ? data.permissions.value
          : this.permissions,
      isSystem: data.isSystem.present ? data.isSystem.value : this.isSystem,
    );
  }

  @override
  String toString() {
    return (StringBuffer('Role(')
          ..write('id: $id, ')
          ..write('name: $name, ')
          ..write('permissions: $permissions, ')
          ..write('isSystem: $isSystem')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(id, name, permissions, isSystem);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is Role &&
          other.id == this.id &&
          other.name == this.name &&
          other.permissions == this.permissions &&
          other.isSystem == this.isSystem);
}

class RolesCompanion extends UpdateCompanion<Role> {
  final Value<String> id;
  final Value<String> name;
  final Value<String> permissions;
  final Value<bool> isSystem;
  final Value<int> rowid;
  const RolesCompanion({
    this.id = const Value.absent(),
    this.name = const Value.absent(),
    this.permissions = const Value.absent(),
    this.isSystem = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  RolesCompanion.insert({
    required String id,
    required String name,
    this.permissions = const Value.absent(),
    this.isSystem = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       name = Value(name);
  static Insertable<Role> custom({
    Expression<String>? id,
    Expression<String>? name,
    Expression<String>? permissions,
    Expression<bool>? isSystem,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (name != null) 'name': name,
      if (permissions != null) 'permissions': permissions,
      if (isSystem != null) 'is_system': isSystem,
      if (rowid != null) 'rowid': rowid,
    });
  }

  RolesCompanion copyWith({
    Value<String>? id,
    Value<String>? name,
    Value<String>? permissions,
    Value<bool>? isSystem,
    Value<int>? rowid,
  }) {
    return RolesCompanion(
      id: id ?? this.id,
      name: name ?? this.name,
      permissions: permissions ?? this.permissions,
      isSystem: isSystem ?? this.isSystem,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (name.present) {
      map['name'] = Variable<String>(name.value);
    }
    if (permissions.present) {
      map['permissions'] = Variable<String>(permissions.value);
    }
    if (isSystem.present) {
      map['is_system'] = Variable<bool>(isSystem.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('RolesCompanion(')
          ..write('id: $id, ')
          ..write('name: $name, ')
          ..write('permissions: $permissions, ')
          ..write('isSystem: $isSystem, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $StaffMembersTable extends StaffMembers
    with TableInfo<$StaffMembersTable, StaffMember> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $StaffMembersTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _nameMeta = const VerificationMeta('name');
  @override
  late final GeneratedColumn<String> name = GeneratedColumn<String>(
    'name',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _mobileMeta = const VerificationMeta('mobile');
  @override
  late final GeneratedColumn<String> mobile = GeneratedColumn<String>(
    'mobile',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _emailMeta = const VerificationMeta('email');
  @override
  late final GeneratedColumn<String> email = GeneratedColumn<String>(
    'email',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _roleIdMeta = const VerificationMeta('roleId');
  @override
  late final GeneratedColumn<String> roleId = GeneratedColumn<String>(
    'role_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES roles (id)',
    ),
  );
  static const VerificationMeta _secretHashMeta = const VerificationMeta(
    'secretHash',
  );
  @override
  late final GeneratedColumn<String> secretHash = GeneratedColumn<String>(
    'secret_hash',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _saltMeta = const VerificationMeta('salt');
  @override
  late final GeneratedColumn<String> salt = GeneratedColumn<String>(
    'salt',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _isActiveMeta = const VerificationMeta(
    'isActive',
  );
  @override
  late final GeneratedColumn<bool> isActive = GeneratedColumn<bool>(
    'is_active',
    aliasedName,
    false,
    type: DriftSqlType.bool,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'CHECK ("is_active" IN (0, 1))',
    ),
    defaultValue: const Constant(true),
  );
  static const VerificationMeta _branchIdsMeta = const VerificationMeta(
    'branchIds',
  );
  @override
  late final GeneratedColumn<String> branchIds = GeneratedColumn<String>(
    'branch_ids',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _siteIdsMeta = const VerificationMeta(
    'siteIds',
  );
  @override
  late final GeneratedColumn<String> siteIds = GeneratedColumn<String>(
    'site_ids',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _emailVerifiedMeta = const VerificationMeta(
    'emailVerified',
  );
  @override
  late final GeneratedColumn<bool> emailVerified = GeneratedColumn<bool>(
    'email_verified',
    aliasedName,
    false,
    type: DriftSqlType.bool,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'CHECK ("email_verified" IN (0, 1))',
    ),
    defaultValue: const Constant(false),
  );
  static const VerificationMeta _createdAtMeta = const VerificationMeta(
    'createdAt',
  );
  @override
  late final GeneratedColumn<DateTime> createdAt = GeneratedColumn<DateTime>(
    'created_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    name,
    mobile,
    email,
    roleId,
    secretHash,
    salt,
    isActive,
    branchIds,
    siteIds,
    emailVerified,
    createdAt,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'staff_members';
  @override
  VerificationContext validateIntegrity(
    Insertable<StaffMember> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('name')) {
      context.handle(
        _nameMeta,
        name.isAcceptableOrUnknown(data['name']!, _nameMeta),
      );
    } else if (isInserting) {
      context.missing(_nameMeta);
    }
    if (data.containsKey('mobile')) {
      context.handle(
        _mobileMeta,
        mobile.isAcceptableOrUnknown(data['mobile']!, _mobileMeta),
      );
    }
    if (data.containsKey('email')) {
      context.handle(
        _emailMeta,
        email.isAcceptableOrUnknown(data['email']!, _emailMeta),
      );
    }
    if (data.containsKey('role_id')) {
      context.handle(
        _roleIdMeta,
        roleId.isAcceptableOrUnknown(data['role_id']!, _roleIdMeta),
      );
    } else if (isInserting) {
      context.missing(_roleIdMeta);
    }
    if (data.containsKey('secret_hash')) {
      context.handle(
        _secretHashMeta,
        secretHash.isAcceptableOrUnknown(data['secret_hash']!, _secretHashMeta),
      );
    } else if (isInserting) {
      context.missing(_secretHashMeta);
    }
    if (data.containsKey('salt')) {
      context.handle(
        _saltMeta,
        salt.isAcceptableOrUnknown(data['salt']!, _saltMeta),
      );
    } else if (isInserting) {
      context.missing(_saltMeta);
    }
    if (data.containsKey('is_active')) {
      context.handle(
        _isActiveMeta,
        isActive.isAcceptableOrUnknown(data['is_active']!, _isActiveMeta),
      );
    }
    if (data.containsKey('branch_ids')) {
      context.handle(
        _branchIdsMeta,
        branchIds.isAcceptableOrUnknown(data['branch_ids']!, _branchIdsMeta),
      );
    }
    if (data.containsKey('site_ids')) {
      context.handle(
        _siteIdsMeta,
        siteIds.isAcceptableOrUnknown(data['site_ids']!, _siteIdsMeta),
      );
    }
    if (data.containsKey('email_verified')) {
      context.handle(
        _emailVerifiedMeta,
        emailVerified.isAcceptableOrUnknown(
          data['email_verified']!,
          _emailVerifiedMeta,
        ),
      );
    }
    if (data.containsKey('created_at')) {
      context.handle(
        _createdAtMeta,
        createdAt.isAcceptableOrUnknown(data['created_at']!, _createdAtMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  StaffMember map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return StaffMember(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      name: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}name'],
      )!,
      mobile: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}mobile'],
      )!,
      email: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}email'],
      )!,
      roleId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}role_id'],
      )!,
      secretHash: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}secret_hash'],
      )!,
      salt: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}salt'],
      )!,
      isActive: attachedDatabase.typeMapping.read(
        DriftSqlType.bool,
        data['${effectivePrefix}is_active'],
      )!,
      branchIds: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}branch_ids'],
      )!,
      siteIds: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}site_ids'],
      )!,
      emailVerified: attachedDatabase.typeMapping.read(
        DriftSqlType.bool,
        data['${effectivePrefix}email_verified'],
      )!,
      createdAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}created_at'],
      )!,
    );
  }

  @override
  $StaffMembersTable createAlias(String alias) {
    return $StaffMembersTable(attachedDatabase, alias);
  }
}

class StaffMember extends DataClass implements Insertable<StaffMember> {
  final String id;
  final String name;
  final String mobile;
  final String email;
  final String roleId;

  /// Password (owner) or PIN (staff), salted + hashed.
  final String secretHash;
  final String salt;
  final bool isActive;

  /// Comma separated branch ids this person may work in. Empty = all.
  final String branchIds;

  /// Comma separated site ids a supervisor is limited to. Empty = all.
  final String siteIds;
  final bool emailVerified;
  final DateTime createdAt;
  const StaffMember({
    required this.id,
    required this.name,
    required this.mobile,
    required this.email,
    required this.roleId,
    required this.secretHash,
    required this.salt,
    required this.isActive,
    required this.branchIds,
    required this.siteIds,
    required this.emailVerified,
    required this.createdAt,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['name'] = Variable<String>(name);
    map['mobile'] = Variable<String>(mobile);
    map['email'] = Variable<String>(email);
    map['role_id'] = Variable<String>(roleId);
    map['secret_hash'] = Variable<String>(secretHash);
    map['salt'] = Variable<String>(salt);
    map['is_active'] = Variable<bool>(isActive);
    map['branch_ids'] = Variable<String>(branchIds);
    map['site_ids'] = Variable<String>(siteIds);
    map['email_verified'] = Variable<bool>(emailVerified);
    map['created_at'] = Variable<DateTime>(createdAt);
    return map;
  }

  StaffMembersCompanion toCompanion(bool nullToAbsent) {
    return StaffMembersCompanion(
      id: Value(id),
      name: Value(name),
      mobile: Value(mobile),
      email: Value(email),
      roleId: Value(roleId),
      secretHash: Value(secretHash),
      salt: Value(salt),
      isActive: Value(isActive),
      branchIds: Value(branchIds),
      siteIds: Value(siteIds),
      emailVerified: Value(emailVerified),
      createdAt: Value(createdAt),
    );
  }

  factory StaffMember.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return StaffMember(
      id: serializer.fromJson<String>(json['id']),
      name: serializer.fromJson<String>(json['name']),
      mobile: serializer.fromJson<String>(json['mobile']),
      email: serializer.fromJson<String>(json['email']),
      roleId: serializer.fromJson<String>(json['roleId']),
      secretHash: serializer.fromJson<String>(json['secretHash']),
      salt: serializer.fromJson<String>(json['salt']),
      isActive: serializer.fromJson<bool>(json['isActive']),
      branchIds: serializer.fromJson<String>(json['branchIds']),
      siteIds: serializer.fromJson<String>(json['siteIds']),
      emailVerified: serializer.fromJson<bool>(json['emailVerified']),
      createdAt: serializer.fromJson<DateTime>(json['createdAt']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'name': serializer.toJson<String>(name),
      'mobile': serializer.toJson<String>(mobile),
      'email': serializer.toJson<String>(email),
      'roleId': serializer.toJson<String>(roleId),
      'secretHash': serializer.toJson<String>(secretHash),
      'salt': serializer.toJson<String>(salt),
      'isActive': serializer.toJson<bool>(isActive),
      'branchIds': serializer.toJson<String>(branchIds),
      'siteIds': serializer.toJson<String>(siteIds),
      'emailVerified': serializer.toJson<bool>(emailVerified),
      'createdAt': serializer.toJson<DateTime>(createdAt),
    };
  }

  StaffMember copyWith({
    String? id,
    String? name,
    String? mobile,
    String? email,
    String? roleId,
    String? secretHash,
    String? salt,
    bool? isActive,
    String? branchIds,
    String? siteIds,
    bool? emailVerified,
    DateTime? createdAt,
  }) => StaffMember(
    id: id ?? this.id,
    name: name ?? this.name,
    mobile: mobile ?? this.mobile,
    email: email ?? this.email,
    roleId: roleId ?? this.roleId,
    secretHash: secretHash ?? this.secretHash,
    salt: salt ?? this.salt,
    isActive: isActive ?? this.isActive,
    branchIds: branchIds ?? this.branchIds,
    siteIds: siteIds ?? this.siteIds,
    emailVerified: emailVerified ?? this.emailVerified,
    createdAt: createdAt ?? this.createdAt,
  );
  StaffMember copyWithCompanion(StaffMembersCompanion data) {
    return StaffMember(
      id: data.id.present ? data.id.value : this.id,
      name: data.name.present ? data.name.value : this.name,
      mobile: data.mobile.present ? data.mobile.value : this.mobile,
      email: data.email.present ? data.email.value : this.email,
      roleId: data.roleId.present ? data.roleId.value : this.roleId,
      secretHash: data.secretHash.present
          ? data.secretHash.value
          : this.secretHash,
      salt: data.salt.present ? data.salt.value : this.salt,
      isActive: data.isActive.present ? data.isActive.value : this.isActive,
      branchIds: data.branchIds.present ? data.branchIds.value : this.branchIds,
      siteIds: data.siteIds.present ? data.siteIds.value : this.siteIds,
      emailVerified: data.emailVerified.present
          ? data.emailVerified.value
          : this.emailVerified,
      createdAt: data.createdAt.present ? data.createdAt.value : this.createdAt,
    );
  }

  @override
  String toString() {
    return (StringBuffer('StaffMember(')
          ..write('id: $id, ')
          ..write('name: $name, ')
          ..write('mobile: $mobile, ')
          ..write('email: $email, ')
          ..write('roleId: $roleId, ')
          ..write('secretHash: $secretHash, ')
          ..write('salt: $salt, ')
          ..write('isActive: $isActive, ')
          ..write('branchIds: $branchIds, ')
          ..write('siteIds: $siteIds, ')
          ..write('emailVerified: $emailVerified, ')
          ..write('createdAt: $createdAt')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    id,
    name,
    mobile,
    email,
    roleId,
    secretHash,
    salt,
    isActive,
    branchIds,
    siteIds,
    emailVerified,
    createdAt,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is StaffMember &&
          other.id == this.id &&
          other.name == this.name &&
          other.mobile == this.mobile &&
          other.email == this.email &&
          other.roleId == this.roleId &&
          other.secretHash == this.secretHash &&
          other.salt == this.salt &&
          other.isActive == this.isActive &&
          other.branchIds == this.branchIds &&
          other.siteIds == this.siteIds &&
          other.emailVerified == this.emailVerified &&
          other.createdAt == this.createdAt);
}

class StaffMembersCompanion extends UpdateCompanion<StaffMember> {
  final Value<String> id;
  final Value<String> name;
  final Value<String> mobile;
  final Value<String> email;
  final Value<String> roleId;
  final Value<String> secretHash;
  final Value<String> salt;
  final Value<bool> isActive;
  final Value<String> branchIds;
  final Value<String> siteIds;
  final Value<bool> emailVerified;
  final Value<DateTime> createdAt;
  final Value<int> rowid;
  const StaffMembersCompanion({
    this.id = const Value.absent(),
    this.name = const Value.absent(),
    this.mobile = const Value.absent(),
    this.email = const Value.absent(),
    this.roleId = const Value.absent(),
    this.secretHash = const Value.absent(),
    this.salt = const Value.absent(),
    this.isActive = const Value.absent(),
    this.branchIds = const Value.absent(),
    this.siteIds = const Value.absent(),
    this.emailVerified = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  StaffMembersCompanion.insert({
    required String id,
    required String name,
    this.mobile = const Value.absent(),
    this.email = const Value.absent(),
    required String roleId,
    required String secretHash,
    required String salt,
    this.isActive = const Value.absent(),
    this.branchIds = const Value.absent(),
    this.siteIds = const Value.absent(),
    this.emailVerified = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       name = Value(name),
       roleId = Value(roleId),
       secretHash = Value(secretHash),
       salt = Value(salt);
  static Insertable<StaffMember> custom({
    Expression<String>? id,
    Expression<String>? name,
    Expression<String>? mobile,
    Expression<String>? email,
    Expression<String>? roleId,
    Expression<String>? secretHash,
    Expression<String>? salt,
    Expression<bool>? isActive,
    Expression<String>? branchIds,
    Expression<String>? siteIds,
    Expression<bool>? emailVerified,
    Expression<DateTime>? createdAt,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (name != null) 'name': name,
      if (mobile != null) 'mobile': mobile,
      if (email != null) 'email': email,
      if (roleId != null) 'role_id': roleId,
      if (secretHash != null) 'secret_hash': secretHash,
      if (salt != null) 'salt': salt,
      if (isActive != null) 'is_active': isActive,
      if (branchIds != null) 'branch_ids': branchIds,
      if (siteIds != null) 'site_ids': siteIds,
      if (emailVerified != null) 'email_verified': emailVerified,
      if (createdAt != null) 'created_at': createdAt,
      if (rowid != null) 'rowid': rowid,
    });
  }

  StaffMembersCompanion copyWith({
    Value<String>? id,
    Value<String>? name,
    Value<String>? mobile,
    Value<String>? email,
    Value<String>? roleId,
    Value<String>? secretHash,
    Value<String>? salt,
    Value<bool>? isActive,
    Value<String>? branchIds,
    Value<String>? siteIds,
    Value<bool>? emailVerified,
    Value<DateTime>? createdAt,
    Value<int>? rowid,
  }) {
    return StaffMembersCompanion(
      id: id ?? this.id,
      name: name ?? this.name,
      mobile: mobile ?? this.mobile,
      email: email ?? this.email,
      roleId: roleId ?? this.roleId,
      secretHash: secretHash ?? this.secretHash,
      salt: salt ?? this.salt,
      isActive: isActive ?? this.isActive,
      branchIds: branchIds ?? this.branchIds,
      siteIds: siteIds ?? this.siteIds,
      emailVerified: emailVerified ?? this.emailVerified,
      createdAt: createdAt ?? this.createdAt,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (name.present) {
      map['name'] = Variable<String>(name.value);
    }
    if (mobile.present) {
      map['mobile'] = Variable<String>(mobile.value);
    }
    if (email.present) {
      map['email'] = Variable<String>(email.value);
    }
    if (roleId.present) {
      map['role_id'] = Variable<String>(roleId.value);
    }
    if (secretHash.present) {
      map['secret_hash'] = Variable<String>(secretHash.value);
    }
    if (salt.present) {
      map['salt'] = Variable<String>(salt.value);
    }
    if (isActive.present) {
      map['is_active'] = Variable<bool>(isActive.value);
    }
    if (branchIds.present) {
      map['branch_ids'] = Variable<String>(branchIds.value);
    }
    if (siteIds.present) {
      map['site_ids'] = Variable<String>(siteIds.value);
    }
    if (emailVerified.present) {
      map['email_verified'] = Variable<bool>(emailVerified.value);
    }
    if (createdAt.present) {
      map['created_at'] = Variable<DateTime>(createdAt.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('StaffMembersCompanion(')
          ..write('id: $id, ')
          ..write('name: $name, ')
          ..write('mobile: $mobile, ')
          ..write('email: $email, ')
          ..write('roleId: $roleId, ')
          ..write('secretHash: $secretHash, ')
          ..write('salt: $salt, ')
          ..write('isActive: $isActive, ')
          ..write('branchIds: $branchIds, ')
          ..write('siteIds: $siteIds, ')
          ..write('emailVerified: $emailVerified, ')
          ..write('createdAt: $createdAt, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $CompaniesTable extends Companies
    with TableInfo<$CompaniesTable, Company> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $CompaniesTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _branchIdMeta = const VerificationMeta(
    'branchId',
  );
  @override
  late final GeneratedColumn<String> branchId = GeneratedColumn<String>(
    'branch_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES branches (id)',
    ),
  );
  static const VerificationMeta _nameMeta = const VerificationMeta('name');
  @override
  late final GeneratedColumn<String> name = GeneratedColumn<String>(
    'name',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _contactPersonMeta = const VerificationMeta(
    'contactPerson',
  );
  @override
  late final GeneratedColumn<String> contactPerson = GeneratedColumn<String>(
    'contact_person',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _mobileMeta = const VerificationMeta('mobile');
  @override
  late final GeneratedColumn<String> mobile = GeneratedColumn<String>(
    'mobile',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _emailMeta = const VerificationMeta('email');
  @override
  late final GeneratedColumn<String> email = GeneratedColumn<String>(
    'email',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _addressMeta = const VerificationMeta(
    'address',
  );
  @override
  late final GeneratedColumn<String> address = GeneratedColumn<String>(
    'address',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _gstinMeta = const VerificationMeta('gstin');
  @override
  late final GeneratedColumn<String> gstin = GeneratedColumn<String>(
    'gstin',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _isActiveMeta = const VerificationMeta(
    'isActive',
  );
  @override
  late final GeneratedColumn<bool> isActive = GeneratedColumn<bool>(
    'is_active',
    aliasedName,
    false,
    type: DriftSqlType.bool,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'CHECK ("is_active" IN (0, 1))',
    ),
    defaultValue: const Constant(true),
  );
  static const VerificationMeta _createdAtMeta = const VerificationMeta(
    'createdAt',
  );
  @override
  late final GeneratedColumn<DateTime> createdAt = GeneratedColumn<DateTime>(
    'created_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    branchId,
    name,
    contactPerson,
    mobile,
    email,
    address,
    gstin,
    isActive,
    createdAt,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'companies';
  @override
  VerificationContext validateIntegrity(
    Insertable<Company> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('branch_id')) {
      context.handle(
        _branchIdMeta,
        branchId.isAcceptableOrUnknown(data['branch_id']!, _branchIdMeta),
      );
    } else if (isInserting) {
      context.missing(_branchIdMeta);
    }
    if (data.containsKey('name')) {
      context.handle(
        _nameMeta,
        name.isAcceptableOrUnknown(data['name']!, _nameMeta),
      );
    } else if (isInserting) {
      context.missing(_nameMeta);
    }
    if (data.containsKey('contact_person')) {
      context.handle(
        _contactPersonMeta,
        contactPerson.isAcceptableOrUnknown(
          data['contact_person']!,
          _contactPersonMeta,
        ),
      );
    }
    if (data.containsKey('mobile')) {
      context.handle(
        _mobileMeta,
        mobile.isAcceptableOrUnknown(data['mobile']!, _mobileMeta),
      );
    }
    if (data.containsKey('email')) {
      context.handle(
        _emailMeta,
        email.isAcceptableOrUnknown(data['email']!, _emailMeta),
      );
    }
    if (data.containsKey('address')) {
      context.handle(
        _addressMeta,
        address.isAcceptableOrUnknown(data['address']!, _addressMeta),
      );
    }
    if (data.containsKey('gstin')) {
      context.handle(
        _gstinMeta,
        gstin.isAcceptableOrUnknown(data['gstin']!, _gstinMeta),
      );
    }
    if (data.containsKey('is_active')) {
      context.handle(
        _isActiveMeta,
        isActive.isAcceptableOrUnknown(data['is_active']!, _isActiveMeta),
      );
    }
    if (data.containsKey('created_at')) {
      context.handle(
        _createdAtMeta,
        createdAt.isAcceptableOrUnknown(data['created_at']!, _createdAtMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  Company map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return Company(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      branchId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}branch_id'],
      )!,
      name: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}name'],
      )!,
      contactPerson: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}contact_person'],
      )!,
      mobile: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}mobile'],
      )!,
      email: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}email'],
      )!,
      address: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}address'],
      )!,
      gstin: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}gstin'],
      )!,
      isActive: attachedDatabase.typeMapping.read(
        DriftSqlType.bool,
        data['${effectivePrefix}is_active'],
      )!,
      createdAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}created_at'],
      )!,
    );
  }

  @override
  $CompaniesTable createAlias(String alias) {
    return $CompaniesTable(attachedDatabase, alias);
  }
}

class Company extends DataClass implements Insertable<Company> {
  final String id;
  final String branchId;
  final String name;
  final String contactPerson;
  final String mobile;
  final String email;
  final String address;
  final String gstin;
  final bool isActive;
  final DateTime createdAt;
  const Company({
    required this.id,
    required this.branchId,
    required this.name,
    required this.contactPerson,
    required this.mobile,
    required this.email,
    required this.address,
    required this.gstin,
    required this.isActive,
    required this.createdAt,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['branch_id'] = Variable<String>(branchId);
    map['name'] = Variable<String>(name);
    map['contact_person'] = Variable<String>(contactPerson);
    map['mobile'] = Variable<String>(mobile);
    map['email'] = Variable<String>(email);
    map['address'] = Variable<String>(address);
    map['gstin'] = Variable<String>(gstin);
    map['is_active'] = Variable<bool>(isActive);
    map['created_at'] = Variable<DateTime>(createdAt);
    return map;
  }

  CompaniesCompanion toCompanion(bool nullToAbsent) {
    return CompaniesCompanion(
      id: Value(id),
      branchId: Value(branchId),
      name: Value(name),
      contactPerson: Value(contactPerson),
      mobile: Value(mobile),
      email: Value(email),
      address: Value(address),
      gstin: Value(gstin),
      isActive: Value(isActive),
      createdAt: Value(createdAt),
    );
  }

  factory Company.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return Company(
      id: serializer.fromJson<String>(json['id']),
      branchId: serializer.fromJson<String>(json['branchId']),
      name: serializer.fromJson<String>(json['name']),
      contactPerson: serializer.fromJson<String>(json['contactPerson']),
      mobile: serializer.fromJson<String>(json['mobile']),
      email: serializer.fromJson<String>(json['email']),
      address: serializer.fromJson<String>(json['address']),
      gstin: serializer.fromJson<String>(json['gstin']),
      isActive: serializer.fromJson<bool>(json['isActive']),
      createdAt: serializer.fromJson<DateTime>(json['createdAt']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'branchId': serializer.toJson<String>(branchId),
      'name': serializer.toJson<String>(name),
      'contactPerson': serializer.toJson<String>(contactPerson),
      'mobile': serializer.toJson<String>(mobile),
      'email': serializer.toJson<String>(email),
      'address': serializer.toJson<String>(address),
      'gstin': serializer.toJson<String>(gstin),
      'isActive': serializer.toJson<bool>(isActive),
      'createdAt': serializer.toJson<DateTime>(createdAt),
    };
  }

  Company copyWith({
    String? id,
    String? branchId,
    String? name,
    String? contactPerson,
    String? mobile,
    String? email,
    String? address,
    String? gstin,
    bool? isActive,
    DateTime? createdAt,
  }) => Company(
    id: id ?? this.id,
    branchId: branchId ?? this.branchId,
    name: name ?? this.name,
    contactPerson: contactPerson ?? this.contactPerson,
    mobile: mobile ?? this.mobile,
    email: email ?? this.email,
    address: address ?? this.address,
    gstin: gstin ?? this.gstin,
    isActive: isActive ?? this.isActive,
    createdAt: createdAt ?? this.createdAt,
  );
  Company copyWithCompanion(CompaniesCompanion data) {
    return Company(
      id: data.id.present ? data.id.value : this.id,
      branchId: data.branchId.present ? data.branchId.value : this.branchId,
      name: data.name.present ? data.name.value : this.name,
      contactPerson: data.contactPerson.present
          ? data.contactPerson.value
          : this.contactPerson,
      mobile: data.mobile.present ? data.mobile.value : this.mobile,
      email: data.email.present ? data.email.value : this.email,
      address: data.address.present ? data.address.value : this.address,
      gstin: data.gstin.present ? data.gstin.value : this.gstin,
      isActive: data.isActive.present ? data.isActive.value : this.isActive,
      createdAt: data.createdAt.present ? data.createdAt.value : this.createdAt,
    );
  }

  @override
  String toString() {
    return (StringBuffer('Company(')
          ..write('id: $id, ')
          ..write('branchId: $branchId, ')
          ..write('name: $name, ')
          ..write('contactPerson: $contactPerson, ')
          ..write('mobile: $mobile, ')
          ..write('email: $email, ')
          ..write('address: $address, ')
          ..write('gstin: $gstin, ')
          ..write('isActive: $isActive, ')
          ..write('createdAt: $createdAt')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    id,
    branchId,
    name,
    contactPerson,
    mobile,
    email,
    address,
    gstin,
    isActive,
    createdAt,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is Company &&
          other.id == this.id &&
          other.branchId == this.branchId &&
          other.name == this.name &&
          other.contactPerson == this.contactPerson &&
          other.mobile == this.mobile &&
          other.email == this.email &&
          other.address == this.address &&
          other.gstin == this.gstin &&
          other.isActive == this.isActive &&
          other.createdAt == this.createdAt);
}

class CompaniesCompanion extends UpdateCompanion<Company> {
  final Value<String> id;
  final Value<String> branchId;
  final Value<String> name;
  final Value<String> contactPerson;
  final Value<String> mobile;
  final Value<String> email;
  final Value<String> address;
  final Value<String> gstin;
  final Value<bool> isActive;
  final Value<DateTime> createdAt;
  final Value<int> rowid;
  const CompaniesCompanion({
    this.id = const Value.absent(),
    this.branchId = const Value.absent(),
    this.name = const Value.absent(),
    this.contactPerson = const Value.absent(),
    this.mobile = const Value.absent(),
    this.email = const Value.absent(),
    this.address = const Value.absent(),
    this.gstin = const Value.absent(),
    this.isActive = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  CompaniesCompanion.insert({
    required String id,
    required String branchId,
    required String name,
    this.contactPerson = const Value.absent(),
    this.mobile = const Value.absent(),
    this.email = const Value.absent(),
    this.address = const Value.absent(),
    this.gstin = const Value.absent(),
    this.isActive = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       branchId = Value(branchId),
       name = Value(name);
  static Insertable<Company> custom({
    Expression<String>? id,
    Expression<String>? branchId,
    Expression<String>? name,
    Expression<String>? contactPerson,
    Expression<String>? mobile,
    Expression<String>? email,
    Expression<String>? address,
    Expression<String>? gstin,
    Expression<bool>? isActive,
    Expression<DateTime>? createdAt,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (branchId != null) 'branch_id': branchId,
      if (name != null) 'name': name,
      if (contactPerson != null) 'contact_person': contactPerson,
      if (mobile != null) 'mobile': mobile,
      if (email != null) 'email': email,
      if (address != null) 'address': address,
      if (gstin != null) 'gstin': gstin,
      if (isActive != null) 'is_active': isActive,
      if (createdAt != null) 'created_at': createdAt,
      if (rowid != null) 'rowid': rowid,
    });
  }

  CompaniesCompanion copyWith({
    Value<String>? id,
    Value<String>? branchId,
    Value<String>? name,
    Value<String>? contactPerson,
    Value<String>? mobile,
    Value<String>? email,
    Value<String>? address,
    Value<String>? gstin,
    Value<bool>? isActive,
    Value<DateTime>? createdAt,
    Value<int>? rowid,
  }) {
    return CompaniesCompanion(
      id: id ?? this.id,
      branchId: branchId ?? this.branchId,
      name: name ?? this.name,
      contactPerson: contactPerson ?? this.contactPerson,
      mobile: mobile ?? this.mobile,
      email: email ?? this.email,
      address: address ?? this.address,
      gstin: gstin ?? this.gstin,
      isActive: isActive ?? this.isActive,
      createdAt: createdAt ?? this.createdAt,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (branchId.present) {
      map['branch_id'] = Variable<String>(branchId.value);
    }
    if (name.present) {
      map['name'] = Variable<String>(name.value);
    }
    if (contactPerson.present) {
      map['contact_person'] = Variable<String>(contactPerson.value);
    }
    if (mobile.present) {
      map['mobile'] = Variable<String>(mobile.value);
    }
    if (email.present) {
      map['email'] = Variable<String>(email.value);
    }
    if (address.present) {
      map['address'] = Variable<String>(address.value);
    }
    if (gstin.present) {
      map['gstin'] = Variable<String>(gstin.value);
    }
    if (isActive.present) {
      map['is_active'] = Variable<bool>(isActive.value);
    }
    if (createdAt.present) {
      map['created_at'] = Variable<DateTime>(createdAt.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('CompaniesCompanion(')
          ..write('id: $id, ')
          ..write('branchId: $branchId, ')
          ..write('name: $name, ')
          ..write('contactPerson: $contactPerson, ')
          ..write('mobile: $mobile, ')
          ..write('email: $email, ')
          ..write('address: $address, ')
          ..write('gstin: $gstin, ')
          ..write('isActive: $isActive, ')
          ..write('createdAt: $createdAt, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $SitesTable extends Sites with TableInfo<$SitesTable, Site> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $SitesTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _companyIdMeta = const VerificationMeta(
    'companyId',
  );
  @override
  late final GeneratedColumn<String> companyId = GeneratedColumn<String>(
    'company_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES companies (id)',
    ),
  );
  static const VerificationMeta _nameMeta = const VerificationMeta('name');
  @override
  late final GeneratedColumn<String> name = GeneratedColumn<String>(
    'name',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _addressMeta = const VerificationMeta(
    'address',
  );
  @override
  late final GeneratedColumn<String> address = GeneratedColumn<String>(
    'address',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _isActiveMeta = const VerificationMeta(
    'isActive',
  );
  @override
  late final GeneratedColumn<bool> isActive = GeneratedColumn<bool>(
    'is_active',
    aliasedName,
    false,
    type: DriftSqlType.bool,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'CHECK ("is_active" IN (0, 1))',
    ),
    defaultValue: const Constant(true),
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    companyId,
    name,
    address,
    isActive,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'sites';
  @override
  VerificationContext validateIntegrity(
    Insertable<Site> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('company_id')) {
      context.handle(
        _companyIdMeta,
        companyId.isAcceptableOrUnknown(data['company_id']!, _companyIdMeta),
      );
    } else if (isInserting) {
      context.missing(_companyIdMeta);
    }
    if (data.containsKey('name')) {
      context.handle(
        _nameMeta,
        name.isAcceptableOrUnknown(data['name']!, _nameMeta),
      );
    } else if (isInserting) {
      context.missing(_nameMeta);
    }
    if (data.containsKey('address')) {
      context.handle(
        _addressMeta,
        address.isAcceptableOrUnknown(data['address']!, _addressMeta),
      );
    }
    if (data.containsKey('is_active')) {
      context.handle(
        _isActiveMeta,
        isActive.isAcceptableOrUnknown(data['is_active']!, _isActiveMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  Site map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return Site(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      companyId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}company_id'],
      )!,
      name: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}name'],
      )!,
      address: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}address'],
      )!,
      isActive: attachedDatabase.typeMapping.read(
        DriftSqlType.bool,
        data['${effectivePrefix}is_active'],
      )!,
    );
  }

  @override
  $SitesTable createAlias(String alias) {
    return $SitesTable(attachedDatabase, alias);
  }
}

class Site extends DataClass implements Insertable<Site> {
  final String id;
  final String companyId;
  final String name;
  final String address;
  final bool isActive;
  const Site({
    required this.id,
    required this.companyId,
    required this.name,
    required this.address,
    required this.isActive,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['company_id'] = Variable<String>(companyId);
    map['name'] = Variable<String>(name);
    map['address'] = Variable<String>(address);
    map['is_active'] = Variable<bool>(isActive);
    return map;
  }

  SitesCompanion toCompanion(bool nullToAbsent) {
    return SitesCompanion(
      id: Value(id),
      companyId: Value(companyId),
      name: Value(name),
      address: Value(address),
      isActive: Value(isActive),
    );
  }

  factory Site.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return Site(
      id: serializer.fromJson<String>(json['id']),
      companyId: serializer.fromJson<String>(json['companyId']),
      name: serializer.fromJson<String>(json['name']),
      address: serializer.fromJson<String>(json['address']),
      isActive: serializer.fromJson<bool>(json['isActive']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'companyId': serializer.toJson<String>(companyId),
      'name': serializer.toJson<String>(name),
      'address': serializer.toJson<String>(address),
      'isActive': serializer.toJson<bool>(isActive),
    };
  }

  Site copyWith({
    String? id,
    String? companyId,
    String? name,
    String? address,
    bool? isActive,
  }) => Site(
    id: id ?? this.id,
    companyId: companyId ?? this.companyId,
    name: name ?? this.name,
    address: address ?? this.address,
    isActive: isActive ?? this.isActive,
  );
  Site copyWithCompanion(SitesCompanion data) {
    return Site(
      id: data.id.present ? data.id.value : this.id,
      companyId: data.companyId.present ? data.companyId.value : this.companyId,
      name: data.name.present ? data.name.value : this.name,
      address: data.address.present ? data.address.value : this.address,
      isActive: data.isActive.present ? data.isActive.value : this.isActive,
    );
  }

  @override
  String toString() {
    return (StringBuffer('Site(')
          ..write('id: $id, ')
          ..write('companyId: $companyId, ')
          ..write('name: $name, ')
          ..write('address: $address, ')
          ..write('isActive: $isActive')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(id, companyId, name, address, isActive);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is Site &&
          other.id == this.id &&
          other.companyId == this.companyId &&
          other.name == this.name &&
          other.address == this.address &&
          other.isActive == this.isActive);
}

class SitesCompanion extends UpdateCompanion<Site> {
  final Value<String> id;
  final Value<String> companyId;
  final Value<String> name;
  final Value<String> address;
  final Value<bool> isActive;
  final Value<int> rowid;
  const SitesCompanion({
    this.id = const Value.absent(),
    this.companyId = const Value.absent(),
    this.name = const Value.absent(),
    this.address = const Value.absent(),
    this.isActive = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  SitesCompanion.insert({
    required String id,
    required String companyId,
    required String name,
    this.address = const Value.absent(),
    this.isActive = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       companyId = Value(companyId),
       name = Value(name);
  static Insertable<Site> custom({
    Expression<String>? id,
    Expression<String>? companyId,
    Expression<String>? name,
    Expression<String>? address,
    Expression<bool>? isActive,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (companyId != null) 'company_id': companyId,
      if (name != null) 'name': name,
      if (address != null) 'address': address,
      if (isActive != null) 'is_active': isActive,
      if (rowid != null) 'rowid': rowid,
    });
  }

  SitesCompanion copyWith({
    Value<String>? id,
    Value<String>? companyId,
    Value<String>? name,
    Value<String>? address,
    Value<bool>? isActive,
    Value<int>? rowid,
  }) {
    return SitesCompanion(
      id: id ?? this.id,
      companyId: companyId ?? this.companyId,
      name: name ?? this.name,
      address: address ?? this.address,
      isActive: isActive ?? this.isActive,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (companyId.present) {
      map['company_id'] = Variable<String>(companyId.value);
    }
    if (name.present) {
      map['name'] = Variable<String>(name.value);
    }
    if (address.present) {
      map['address'] = Variable<String>(address.value);
    }
    if (isActive.present) {
      map['is_active'] = Variable<bool>(isActive.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('SitesCompanion(')
          ..write('id: $id, ')
          ..write('companyId: $companyId, ')
          ..write('name: $name, ')
          ..write('address: $address, ')
          ..write('isActive: $isActive, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $ContractsTable extends Contracts
    with TableInfo<$ContractsTable, Contract> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $ContractsTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _companyIdMeta = const VerificationMeta(
    'companyId',
  );
  @override
  late final GeneratedColumn<String> companyId = GeneratedColumn<String>(
    'company_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES companies (id)',
    ),
  );
  static const VerificationMeta _titleMeta = const VerificationMeta('title');
  @override
  late final GeneratedColumn<String> title = GeneratedColumn<String>(
    'title',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _startDateMeta = const VerificationMeta(
    'startDate',
  );
  @override
  late final GeneratedColumn<String> startDate = GeneratedColumn<String>(
    'start_date',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _endDateMeta = const VerificationMeta(
    'endDate',
  );
  @override
  late final GeneratedColumn<String> endDate = GeneratedColumn<String>(
    'end_date',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _paymentTermsDaysMeta = const VerificationMeta(
    'paymentTermsDays',
  );
  @override
  late final GeneratedColumn<int> paymentTermsDays = GeneratedColumn<int>(
    'payment_terms_days',
    aliasedName,
    false,
    type: DriftSqlType.int,
    requiredDuringInsert: false,
    defaultValue: const Constant(30),
  );
  static const VerificationMeta _notesMeta = const VerificationMeta('notes');
  @override
  late final GeneratedColumn<String> notes = GeneratedColumn<String>(
    'notes',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _docPathMeta = const VerificationMeta(
    'docPath',
  );
  @override
  late final GeneratedColumn<String> docPath = GeneratedColumn<String>(
    'doc_path',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _isActiveMeta = const VerificationMeta(
    'isActive',
  );
  @override
  late final GeneratedColumn<bool> isActive = GeneratedColumn<bool>(
    'is_active',
    aliasedName,
    false,
    type: DriftSqlType.bool,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'CHECK ("is_active" IN (0, 1))',
    ),
    defaultValue: const Constant(true),
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    companyId,
    title,
    startDate,
    endDate,
    paymentTermsDays,
    notes,
    docPath,
    isActive,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'contracts';
  @override
  VerificationContext validateIntegrity(
    Insertable<Contract> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('company_id')) {
      context.handle(
        _companyIdMeta,
        companyId.isAcceptableOrUnknown(data['company_id']!, _companyIdMeta),
      );
    } else if (isInserting) {
      context.missing(_companyIdMeta);
    }
    if (data.containsKey('title')) {
      context.handle(
        _titleMeta,
        title.isAcceptableOrUnknown(data['title']!, _titleMeta),
      );
    } else if (isInserting) {
      context.missing(_titleMeta);
    }
    if (data.containsKey('start_date')) {
      context.handle(
        _startDateMeta,
        startDate.isAcceptableOrUnknown(data['start_date']!, _startDateMeta),
      );
    } else if (isInserting) {
      context.missing(_startDateMeta);
    }
    if (data.containsKey('end_date')) {
      context.handle(
        _endDateMeta,
        endDate.isAcceptableOrUnknown(data['end_date']!, _endDateMeta),
      );
    }
    if (data.containsKey('payment_terms_days')) {
      context.handle(
        _paymentTermsDaysMeta,
        paymentTermsDays.isAcceptableOrUnknown(
          data['payment_terms_days']!,
          _paymentTermsDaysMeta,
        ),
      );
    }
    if (data.containsKey('notes')) {
      context.handle(
        _notesMeta,
        notes.isAcceptableOrUnknown(data['notes']!, _notesMeta),
      );
    }
    if (data.containsKey('doc_path')) {
      context.handle(
        _docPathMeta,
        docPath.isAcceptableOrUnknown(data['doc_path']!, _docPathMeta),
      );
    }
    if (data.containsKey('is_active')) {
      context.handle(
        _isActiveMeta,
        isActive.isAcceptableOrUnknown(data['is_active']!, _isActiveMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  Contract map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return Contract(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      companyId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}company_id'],
      )!,
      title: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}title'],
      )!,
      startDate: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}start_date'],
      )!,
      endDate: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}end_date'],
      ),
      paymentTermsDays: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}payment_terms_days'],
      )!,
      notes: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}notes'],
      )!,
      docPath: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}doc_path'],
      ),
      isActive: attachedDatabase.typeMapping.read(
        DriftSqlType.bool,
        data['${effectivePrefix}is_active'],
      )!,
    );
  }

  @override
  $ContractsTable createAlias(String alias) {
    return $ContractsTable(attachedDatabase, alias);
  }
}

class Contract extends DataClass implements Insertable<Contract> {
  final String id;
  final String companyId;
  final String title;
  final String startDate;
  final String? endDate;
  final int paymentTermsDays;
  final String notes;
  final String? docPath;
  final bool isActive;
  const Contract({
    required this.id,
    required this.companyId,
    required this.title,
    required this.startDate,
    this.endDate,
    required this.paymentTermsDays,
    required this.notes,
    this.docPath,
    required this.isActive,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['company_id'] = Variable<String>(companyId);
    map['title'] = Variable<String>(title);
    map['start_date'] = Variable<String>(startDate);
    if (!nullToAbsent || endDate != null) {
      map['end_date'] = Variable<String>(endDate);
    }
    map['payment_terms_days'] = Variable<int>(paymentTermsDays);
    map['notes'] = Variable<String>(notes);
    if (!nullToAbsent || docPath != null) {
      map['doc_path'] = Variable<String>(docPath);
    }
    map['is_active'] = Variable<bool>(isActive);
    return map;
  }

  ContractsCompanion toCompanion(bool nullToAbsent) {
    return ContractsCompanion(
      id: Value(id),
      companyId: Value(companyId),
      title: Value(title),
      startDate: Value(startDate),
      endDate: endDate == null && nullToAbsent
          ? const Value.absent()
          : Value(endDate),
      paymentTermsDays: Value(paymentTermsDays),
      notes: Value(notes),
      docPath: docPath == null && nullToAbsent
          ? const Value.absent()
          : Value(docPath),
      isActive: Value(isActive),
    );
  }

  factory Contract.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return Contract(
      id: serializer.fromJson<String>(json['id']),
      companyId: serializer.fromJson<String>(json['companyId']),
      title: serializer.fromJson<String>(json['title']),
      startDate: serializer.fromJson<String>(json['startDate']),
      endDate: serializer.fromJson<String?>(json['endDate']),
      paymentTermsDays: serializer.fromJson<int>(json['paymentTermsDays']),
      notes: serializer.fromJson<String>(json['notes']),
      docPath: serializer.fromJson<String?>(json['docPath']),
      isActive: serializer.fromJson<bool>(json['isActive']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'companyId': serializer.toJson<String>(companyId),
      'title': serializer.toJson<String>(title),
      'startDate': serializer.toJson<String>(startDate),
      'endDate': serializer.toJson<String?>(endDate),
      'paymentTermsDays': serializer.toJson<int>(paymentTermsDays),
      'notes': serializer.toJson<String>(notes),
      'docPath': serializer.toJson<String?>(docPath),
      'isActive': serializer.toJson<bool>(isActive),
    };
  }

  Contract copyWith({
    String? id,
    String? companyId,
    String? title,
    String? startDate,
    Value<String?> endDate = const Value.absent(),
    int? paymentTermsDays,
    String? notes,
    Value<String?> docPath = const Value.absent(),
    bool? isActive,
  }) => Contract(
    id: id ?? this.id,
    companyId: companyId ?? this.companyId,
    title: title ?? this.title,
    startDate: startDate ?? this.startDate,
    endDate: endDate.present ? endDate.value : this.endDate,
    paymentTermsDays: paymentTermsDays ?? this.paymentTermsDays,
    notes: notes ?? this.notes,
    docPath: docPath.present ? docPath.value : this.docPath,
    isActive: isActive ?? this.isActive,
  );
  Contract copyWithCompanion(ContractsCompanion data) {
    return Contract(
      id: data.id.present ? data.id.value : this.id,
      companyId: data.companyId.present ? data.companyId.value : this.companyId,
      title: data.title.present ? data.title.value : this.title,
      startDate: data.startDate.present ? data.startDate.value : this.startDate,
      endDate: data.endDate.present ? data.endDate.value : this.endDate,
      paymentTermsDays: data.paymentTermsDays.present
          ? data.paymentTermsDays.value
          : this.paymentTermsDays,
      notes: data.notes.present ? data.notes.value : this.notes,
      docPath: data.docPath.present ? data.docPath.value : this.docPath,
      isActive: data.isActive.present ? data.isActive.value : this.isActive,
    );
  }

  @override
  String toString() {
    return (StringBuffer('Contract(')
          ..write('id: $id, ')
          ..write('companyId: $companyId, ')
          ..write('title: $title, ')
          ..write('startDate: $startDate, ')
          ..write('endDate: $endDate, ')
          ..write('paymentTermsDays: $paymentTermsDays, ')
          ..write('notes: $notes, ')
          ..write('docPath: $docPath, ')
          ..write('isActive: $isActive')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    id,
    companyId,
    title,
    startDate,
    endDate,
    paymentTermsDays,
    notes,
    docPath,
    isActive,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is Contract &&
          other.id == this.id &&
          other.companyId == this.companyId &&
          other.title == this.title &&
          other.startDate == this.startDate &&
          other.endDate == this.endDate &&
          other.paymentTermsDays == this.paymentTermsDays &&
          other.notes == this.notes &&
          other.docPath == this.docPath &&
          other.isActive == this.isActive);
}

class ContractsCompanion extends UpdateCompanion<Contract> {
  final Value<String> id;
  final Value<String> companyId;
  final Value<String> title;
  final Value<String> startDate;
  final Value<String?> endDate;
  final Value<int> paymentTermsDays;
  final Value<String> notes;
  final Value<String?> docPath;
  final Value<bool> isActive;
  final Value<int> rowid;
  const ContractsCompanion({
    this.id = const Value.absent(),
    this.companyId = const Value.absent(),
    this.title = const Value.absent(),
    this.startDate = const Value.absent(),
    this.endDate = const Value.absent(),
    this.paymentTermsDays = const Value.absent(),
    this.notes = const Value.absent(),
    this.docPath = const Value.absent(),
    this.isActive = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  ContractsCompanion.insert({
    required String id,
    required String companyId,
    required String title,
    required String startDate,
    this.endDate = const Value.absent(),
    this.paymentTermsDays = const Value.absent(),
    this.notes = const Value.absent(),
    this.docPath = const Value.absent(),
    this.isActive = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       companyId = Value(companyId),
       title = Value(title),
       startDate = Value(startDate);
  static Insertable<Contract> custom({
    Expression<String>? id,
    Expression<String>? companyId,
    Expression<String>? title,
    Expression<String>? startDate,
    Expression<String>? endDate,
    Expression<int>? paymentTermsDays,
    Expression<String>? notes,
    Expression<String>? docPath,
    Expression<bool>? isActive,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (companyId != null) 'company_id': companyId,
      if (title != null) 'title': title,
      if (startDate != null) 'start_date': startDate,
      if (endDate != null) 'end_date': endDate,
      if (paymentTermsDays != null) 'payment_terms_days': paymentTermsDays,
      if (notes != null) 'notes': notes,
      if (docPath != null) 'doc_path': docPath,
      if (isActive != null) 'is_active': isActive,
      if (rowid != null) 'rowid': rowid,
    });
  }

  ContractsCompanion copyWith({
    Value<String>? id,
    Value<String>? companyId,
    Value<String>? title,
    Value<String>? startDate,
    Value<String?>? endDate,
    Value<int>? paymentTermsDays,
    Value<String>? notes,
    Value<String?>? docPath,
    Value<bool>? isActive,
    Value<int>? rowid,
  }) {
    return ContractsCompanion(
      id: id ?? this.id,
      companyId: companyId ?? this.companyId,
      title: title ?? this.title,
      startDate: startDate ?? this.startDate,
      endDate: endDate ?? this.endDate,
      paymentTermsDays: paymentTermsDays ?? this.paymentTermsDays,
      notes: notes ?? this.notes,
      docPath: docPath ?? this.docPath,
      isActive: isActive ?? this.isActive,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (companyId.present) {
      map['company_id'] = Variable<String>(companyId.value);
    }
    if (title.present) {
      map['title'] = Variable<String>(title.value);
    }
    if (startDate.present) {
      map['start_date'] = Variable<String>(startDate.value);
    }
    if (endDate.present) {
      map['end_date'] = Variable<String>(endDate.value);
    }
    if (paymentTermsDays.present) {
      map['payment_terms_days'] = Variable<int>(paymentTermsDays.value);
    }
    if (notes.present) {
      map['notes'] = Variable<String>(notes.value);
    }
    if (docPath.present) {
      map['doc_path'] = Variable<String>(docPath.value);
    }
    if (isActive.present) {
      map['is_active'] = Variable<bool>(isActive.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('ContractsCompanion(')
          ..write('id: $id, ')
          ..write('companyId: $companyId, ')
          ..write('title: $title, ')
          ..write('startDate: $startDate, ')
          ..write('endDate: $endDate, ')
          ..write('paymentTermsDays: $paymentTermsDays, ')
          ..write('notes: $notes, ')
          ..write('docPath: $docPath, ')
          ..write('isActive: $isActive, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $ContractRatesTable extends ContractRates
    with TableInfo<$ContractRatesTable, ContractRate> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $ContractRatesTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _contractIdMeta = const VerificationMeta(
    'contractId',
  );
  @override
  late final GeneratedColumn<String> contractId = GeneratedColumn<String>(
    'contract_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES contracts (id)',
    ),
  );
  static const VerificationMeta _skillMeta = const VerificationMeta('skill');
  @override
  late final GeneratedColumn<String> skill = GeneratedColumn<String>(
    'skill',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _perDayMeta = const VerificationMeta('perDay');
  @override
  late final GeneratedColumn<int> perDay = GeneratedColumn<int>(
    'per_day',
    aliasedName,
    false,
    type: DriftSqlType.int,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _otPerHourMeta = const VerificationMeta(
    'otPerHour',
  );
  @override
  late final GeneratedColumn<int> otPerHour = GeneratedColumn<int>(
    'ot_per_hour',
    aliasedName,
    false,
    type: DriftSqlType.int,
    requiredDuringInsert: false,
    defaultValue: const Constant(0),
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    contractId,
    skill,
    perDay,
    otPerHour,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'contract_rates';
  @override
  VerificationContext validateIntegrity(
    Insertable<ContractRate> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('contract_id')) {
      context.handle(
        _contractIdMeta,
        contractId.isAcceptableOrUnknown(data['contract_id']!, _contractIdMeta),
      );
    } else if (isInserting) {
      context.missing(_contractIdMeta);
    }
    if (data.containsKey('skill')) {
      context.handle(
        _skillMeta,
        skill.isAcceptableOrUnknown(data['skill']!, _skillMeta),
      );
    } else if (isInserting) {
      context.missing(_skillMeta);
    }
    if (data.containsKey('per_day')) {
      context.handle(
        _perDayMeta,
        perDay.isAcceptableOrUnknown(data['per_day']!, _perDayMeta),
      );
    } else if (isInserting) {
      context.missing(_perDayMeta);
    }
    if (data.containsKey('ot_per_hour')) {
      context.handle(
        _otPerHourMeta,
        otPerHour.isAcceptableOrUnknown(data['ot_per_hour']!, _otPerHourMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  ContractRate map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return ContractRate(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      contractId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}contract_id'],
      )!,
      skill: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}skill'],
      )!,
      perDay: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}per_day'],
      )!,
      otPerHour: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}ot_per_hour'],
      )!,
    );
  }

  @override
  $ContractRatesTable createAlias(String alias) {
    return $ContractRatesTable(attachedDatabase, alias);
  }
}

class ContractRate extends DataClass implements Insertable<ContractRate> {
  final String id;
  final String contractId;
  final String skill;
  final int perDay;
  final int otPerHour;
  const ContractRate({
    required this.id,
    required this.contractId,
    required this.skill,
    required this.perDay,
    required this.otPerHour,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['contract_id'] = Variable<String>(contractId);
    map['skill'] = Variable<String>(skill);
    map['per_day'] = Variable<int>(perDay);
    map['ot_per_hour'] = Variable<int>(otPerHour);
    return map;
  }

  ContractRatesCompanion toCompanion(bool nullToAbsent) {
    return ContractRatesCompanion(
      id: Value(id),
      contractId: Value(contractId),
      skill: Value(skill),
      perDay: Value(perDay),
      otPerHour: Value(otPerHour),
    );
  }

  factory ContractRate.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return ContractRate(
      id: serializer.fromJson<String>(json['id']),
      contractId: serializer.fromJson<String>(json['contractId']),
      skill: serializer.fromJson<String>(json['skill']),
      perDay: serializer.fromJson<int>(json['perDay']),
      otPerHour: serializer.fromJson<int>(json['otPerHour']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'contractId': serializer.toJson<String>(contractId),
      'skill': serializer.toJson<String>(skill),
      'perDay': serializer.toJson<int>(perDay),
      'otPerHour': serializer.toJson<int>(otPerHour),
    };
  }

  ContractRate copyWith({
    String? id,
    String? contractId,
    String? skill,
    int? perDay,
    int? otPerHour,
  }) => ContractRate(
    id: id ?? this.id,
    contractId: contractId ?? this.contractId,
    skill: skill ?? this.skill,
    perDay: perDay ?? this.perDay,
    otPerHour: otPerHour ?? this.otPerHour,
  );
  ContractRate copyWithCompanion(ContractRatesCompanion data) {
    return ContractRate(
      id: data.id.present ? data.id.value : this.id,
      contractId: data.contractId.present
          ? data.contractId.value
          : this.contractId,
      skill: data.skill.present ? data.skill.value : this.skill,
      perDay: data.perDay.present ? data.perDay.value : this.perDay,
      otPerHour: data.otPerHour.present ? data.otPerHour.value : this.otPerHour,
    );
  }

  @override
  String toString() {
    return (StringBuffer('ContractRate(')
          ..write('id: $id, ')
          ..write('contractId: $contractId, ')
          ..write('skill: $skill, ')
          ..write('perDay: $perDay, ')
          ..write('otPerHour: $otPerHour')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(id, contractId, skill, perDay, otPerHour);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is ContractRate &&
          other.id == this.id &&
          other.contractId == this.contractId &&
          other.skill == this.skill &&
          other.perDay == this.perDay &&
          other.otPerHour == this.otPerHour);
}

class ContractRatesCompanion extends UpdateCompanion<ContractRate> {
  final Value<String> id;
  final Value<String> contractId;
  final Value<String> skill;
  final Value<int> perDay;
  final Value<int> otPerHour;
  final Value<int> rowid;
  const ContractRatesCompanion({
    this.id = const Value.absent(),
    this.contractId = const Value.absent(),
    this.skill = const Value.absent(),
    this.perDay = const Value.absent(),
    this.otPerHour = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  ContractRatesCompanion.insert({
    required String id,
    required String contractId,
    required String skill,
    required int perDay,
    this.otPerHour = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       contractId = Value(contractId),
       skill = Value(skill),
       perDay = Value(perDay);
  static Insertable<ContractRate> custom({
    Expression<String>? id,
    Expression<String>? contractId,
    Expression<String>? skill,
    Expression<int>? perDay,
    Expression<int>? otPerHour,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (contractId != null) 'contract_id': contractId,
      if (skill != null) 'skill': skill,
      if (perDay != null) 'per_day': perDay,
      if (otPerHour != null) 'ot_per_hour': otPerHour,
      if (rowid != null) 'rowid': rowid,
    });
  }

  ContractRatesCompanion copyWith({
    Value<String>? id,
    Value<String>? contractId,
    Value<String>? skill,
    Value<int>? perDay,
    Value<int>? otPerHour,
    Value<int>? rowid,
  }) {
    return ContractRatesCompanion(
      id: id ?? this.id,
      contractId: contractId ?? this.contractId,
      skill: skill ?? this.skill,
      perDay: perDay ?? this.perDay,
      otPerHour: otPerHour ?? this.otPerHour,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (contractId.present) {
      map['contract_id'] = Variable<String>(contractId.value);
    }
    if (skill.present) {
      map['skill'] = Variable<String>(skill.value);
    }
    if (perDay.present) {
      map['per_day'] = Variable<int>(perDay.value);
    }
    if (otPerHour.present) {
      map['ot_per_hour'] = Variable<int>(otPerHour.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('ContractRatesCompanion(')
          ..write('id: $id, ')
          ..write('contractId: $contractId, ')
          ..write('skill: $skill, ')
          ..write('perDay: $perDay, ')
          ..write('otPerHour: $otPerHour, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $LaboursTable extends Labours with TableInfo<$LaboursTable, Labour> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $LaboursTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _branchIdMeta = const VerificationMeta(
    'branchId',
  );
  @override
  late final GeneratedColumn<String> branchId = GeneratedColumn<String>(
    'branch_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES branches (id)',
    ),
  );
  static const VerificationMeta _nameMeta = const VerificationMeta('name');
  @override
  late final GeneratedColumn<String> name = GeneratedColumn<String>(
    'name',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _fatherNameMeta = const VerificationMeta(
    'fatherName',
  );
  @override
  late final GeneratedColumn<String> fatherName = GeneratedColumn<String>(
    'father_name',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _mobileMeta = const VerificationMeta('mobile');
  @override
  late final GeneratedColumn<String> mobile = GeneratedColumn<String>(
    'mobile',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _addressMeta = const VerificationMeta(
    'address',
  );
  @override
  late final GeneratedColumn<String> address = GeneratedColumn<String>(
    'address',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _skillMeta = const VerificationMeta('skill');
  @override
  late final GeneratedColumn<String> skill = GeneratedColumn<String>(
    'skill',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant('Helper'),
  );
  static const VerificationMeta _photoPathMeta = const VerificationMeta(
    'photoPath',
  );
  @override
  late final GeneratedColumn<String> photoPath = GeneratedColumn<String>(
    'photo_path',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _joinDateMeta = const VerificationMeta(
    'joinDate',
  );
  @override
  late final GeneratedColumn<String> joinDate = GeneratedColumn<String>(
    'join_date',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _leaveDateMeta = const VerificationMeta(
    'leaveDate',
  );
  @override
  late final GeneratedColumn<String> leaveDate = GeneratedColumn<String>(
    'leave_date',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _statusMeta = const VerificationMeta('status');
  @override
  late final GeneratedColumn<String> status = GeneratedColumn<String>(
    'status',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant('active'),
  );
  static const VerificationMeta _noteMeta = const VerificationMeta('note');
  @override
  late final GeneratedColumn<String> note = GeneratedColumn<String>(
    'note',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _createdAtMeta = const VerificationMeta(
    'createdAt',
  );
  @override
  late final GeneratedColumn<DateTime> createdAt = GeneratedColumn<DateTime>(
    'created_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    branchId,
    name,
    fatherName,
    mobile,
    address,
    skill,
    photoPath,
    joinDate,
    leaveDate,
    status,
    note,
    createdAt,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'labours';
  @override
  VerificationContext validateIntegrity(
    Insertable<Labour> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('branch_id')) {
      context.handle(
        _branchIdMeta,
        branchId.isAcceptableOrUnknown(data['branch_id']!, _branchIdMeta),
      );
    } else if (isInserting) {
      context.missing(_branchIdMeta);
    }
    if (data.containsKey('name')) {
      context.handle(
        _nameMeta,
        name.isAcceptableOrUnknown(data['name']!, _nameMeta),
      );
    } else if (isInserting) {
      context.missing(_nameMeta);
    }
    if (data.containsKey('father_name')) {
      context.handle(
        _fatherNameMeta,
        fatherName.isAcceptableOrUnknown(data['father_name']!, _fatherNameMeta),
      );
    }
    if (data.containsKey('mobile')) {
      context.handle(
        _mobileMeta,
        mobile.isAcceptableOrUnknown(data['mobile']!, _mobileMeta),
      );
    }
    if (data.containsKey('address')) {
      context.handle(
        _addressMeta,
        address.isAcceptableOrUnknown(data['address']!, _addressMeta),
      );
    }
    if (data.containsKey('skill')) {
      context.handle(
        _skillMeta,
        skill.isAcceptableOrUnknown(data['skill']!, _skillMeta),
      );
    }
    if (data.containsKey('photo_path')) {
      context.handle(
        _photoPathMeta,
        photoPath.isAcceptableOrUnknown(data['photo_path']!, _photoPathMeta),
      );
    }
    if (data.containsKey('join_date')) {
      context.handle(
        _joinDateMeta,
        joinDate.isAcceptableOrUnknown(data['join_date']!, _joinDateMeta),
      );
    } else if (isInserting) {
      context.missing(_joinDateMeta);
    }
    if (data.containsKey('leave_date')) {
      context.handle(
        _leaveDateMeta,
        leaveDate.isAcceptableOrUnknown(data['leave_date']!, _leaveDateMeta),
      );
    }
    if (data.containsKey('status')) {
      context.handle(
        _statusMeta,
        status.isAcceptableOrUnknown(data['status']!, _statusMeta),
      );
    }
    if (data.containsKey('note')) {
      context.handle(
        _noteMeta,
        note.isAcceptableOrUnknown(data['note']!, _noteMeta),
      );
    }
    if (data.containsKey('created_at')) {
      context.handle(
        _createdAtMeta,
        createdAt.isAcceptableOrUnknown(data['created_at']!, _createdAtMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  Labour map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return Labour(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      branchId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}branch_id'],
      )!,
      name: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}name'],
      )!,
      fatherName: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}father_name'],
      )!,
      mobile: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}mobile'],
      )!,
      address: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}address'],
      )!,
      skill: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}skill'],
      )!,
      photoPath: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}photo_path'],
      ),
      joinDate: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}join_date'],
      )!,
      leaveDate: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}leave_date'],
      ),
      status: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}status'],
      )!,
      note: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}note'],
      )!,
      createdAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}created_at'],
      )!,
    );
  }

  @override
  $LaboursTable createAlias(String alias) {
    return $LaboursTable(attachedDatabase, alias);
  }
}

class Labour extends DataClass implements Insertable<Labour> {
  final String id;
  final String branchId;
  final String name;
  final String fatherName;
  final String mobile;
  final String address;
  final String skill;
  final String? photoPath;
  final String joinDate;
  final String? leaveDate;

  /// active | inactive | left
  final String status;
  final String note;
  final DateTime createdAt;
  const Labour({
    required this.id,
    required this.branchId,
    required this.name,
    required this.fatherName,
    required this.mobile,
    required this.address,
    required this.skill,
    this.photoPath,
    required this.joinDate,
    this.leaveDate,
    required this.status,
    required this.note,
    required this.createdAt,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['branch_id'] = Variable<String>(branchId);
    map['name'] = Variable<String>(name);
    map['father_name'] = Variable<String>(fatherName);
    map['mobile'] = Variable<String>(mobile);
    map['address'] = Variable<String>(address);
    map['skill'] = Variable<String>(skill);
    if (!nullToAbsent || photoPath != null) {
      map['photo_path'] = Variable<String>(photoPath);
    }
    map['join_date'] = Variable<String>(joinDate);
    if (!nullToAbsent || leaveDate != null) {
      map['leave_date'] = Variable<String>(leaveDate);
    }
    map['status'] = Variable<String>(status);
    map['note'] = Variable<String>(note);
    map['created_at'] = Variable<DateTime>(createdAt);
    return map;
  }

  LaboursCompanion toCompanion(bool nullToAbsent) {
    return LaboursCompanion(
      id: Value(id),
      branchId: Value(branchId),
      name: Value(name),
      fatherName: Value(fatherName),
      mobile: Value(mobile),
      address: Value(address),
      skill: Value(skill),
      photoPath: photoPath == null && nullToAbsent
          ? const Value.absent()
          : Value(photoPath),
      joinDate: Value(joinDate),
      leaveDate: leaveDate == null && nullToAbsent
          ? const Value.absent()
          : Value(leaveDate),
      status: Value(status),
      note: Value(note),
      createdAt: Value(createdAt),
    );
  }

  factory Labour.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return Labour(
      id: serializer.fromJson<String>(json['id']),
      branchId: serializer.fromJson<String>(json['branchId']),
      name: serializer.fromJson<String>(json['name']),
      fatherName: serializer.fromJson<String>(json['fatherName']),
      mobile: serializer.fromJson<String>(json['mobile']),
      address: serializer.fromJson<String>(json['address']),
      skill: serializer.fromJson<String>(json['skill']),
      photoPath: serializer.fromJson<String?>(json['photoPath']),
      joinDate: serializer.fromJson<String>(json['joinDate']),
      leaveDate: serializer.fromJson<String?>(json['leaveDate']),
      status: serializer.fromJson<String>(json['status']),
      note: serializer.fromJson<String>(json['note']),
      createdAt: serializer.fromJson<DateTime>(json['createdAt']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'branchId': serializer.toJson<String>(branchId),
      'name': serializer.toJson<String>(name),
      'fatherName': serializer.toJson<String>(fatherName),
      'mobile': serializer.toJson<String>(mobile),
      'address': serializer.toJson<String>(address),
      'skill': serializer.toJson<String>(skill),
      'photoPath': serializer.toJson<String?>(photoPath),
      'joinDate': serializer.toJson<String>(joinDate),
      'leaveDate': serializer.toJson<String?>(leaveDate),
      'status': serializer.toJson<String>(status),
      'note': serializer.toJson<String>(note),
      'createdAt': serializer.toJson<DateTime>(createdAt),
    };
  }

  Labour copyWith({
    String? id,
    String? branchId,
    String? name,
    String? fatherName,
    String? mobile,
    String? address,
    String? skill,
    Value<String?> photoPath = const Value.absent(),
    String? joinDate,
    Value<String?> leaveDate = const Value.absent(),
    String? status,
    String? note,
    DateTime? createdAt,
  }) => Labour(
    id: id ?? this.id,
    branchId: branchId ?? this.branchId,
    name: name ?? this.name,
    fatherName: fatherName ?? this.fatherName,
    mobile: mobile ?? this.mobile,
    address: address ?? this.address,
    skill: skill ?? this.skill,
    photoPath: photoPath.present ? photoPath.value : this.photoPath,
    joinDate: joinDate ?? this.joinDate,
    leaveDate: leaveDate.present ? leaveDate.value : this.leaveDate,
    status: status ?? this.status,
    note: note ?? this.note,
    createdAt: createdAt ?? this.createdAt,
  );
  Labour copyWithCompanion(LaboursCompanion data) {
    return Labour(
      id: data.id.present ? data.id.value : this.id,
      branchId: data.branchId.present ? data.branchId.value : this.branchId,
      name: data.name.present ? data.name.value : this.name,
      fatherName: data.fatherName.present
          ? data.fatherName.value
          : this.fatherName,
      mobile: data.mobile.present ? data.mobile.value : this.mobile,
      address: data.address.present ? data.address.value : this.address,
      skill: data.skill.present ? data.skill.value : this.skill,
      photoPath: data.photoPath.present ? data.photoPath.value : this.photoPath,
      joinDate: data.joinDate.present ? data.joinDate.value : this.joinDate,
      leaveDate: data.leaveDate.present ? data.leaveDate.value : this.leaveDate,
      status: data.status.present ? data.status.value : this.status,
      note: data.note.present ? data.note.value : this.note,
      createdAt: data.createdAt.present ? data.createdAt.value : this.createdAt,
    );
  }

  @override
  String toString() {
    return (StringBuffer('Labour(')
          ..write('id: $id, ')
          ..write('branchId: $branchId, ')
          ..write('name: $name, ')
          ..write('fatherName: $fatherName, ')
          ..write('mobile: $mobile, ')
          ..write('address: $address, ')
          ..write('skill: $skill, ')
          ..write('photoPath: $photoPath, ')
          ..write('joinDate: $joinDate, ')
          ..write('leaveDate: $leaveDate, ')
          ..write('status: $status, ')
          ..write('note: $note, ')
          ..write('createdAt: $createdAt')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    id,
    branchId,
    name,
    fatherName,
    mobile,
    address,
    skill,
    photoPath,
    joinDate,
    leaveDate,
    status,
    note,
    createdAt,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is Labour &&
          other.id == this.id &&
          other.branchId == this.branchId &&
          other.name == this.name &&
          other.fatherName == this.fatherName &&
          other.mobile == this.mobile &&
          other.address == this.address &&
          other.skill == this.skill &&
          other.photoPath == this.photoPath &&
          other.joinDate == this.joinDate &&
          other.leaveDate == this.leaveDate &&
          other.status == this.status &&
          other.note == this.note &&
          other.createdAt == this.createdAt);
}

class LaboursCompanion extends UpdateCompanion<Labour> {
  final Value<String> id;
  final Value<String> branchId;
  final Value<String> name;
  final Value<String> fatherName;
  final Value<String> mobile;
  final Value<String> address;
  final Value<String> skill;
  final Value<String?> photoPath;
  final Value<String> joinDate;
  final Value<String?> leaveDate;
  final Value<String> status;
  final Value<String> note;
  final Value<DateTime> createdAt;
  final Value<int> rowid;
  const LaboursCompanion({
    this.id = const Value.absent(),
    this.branchId = const Value.absent(),
    this.name = const Value.absent(),
    this.fatherName = const Value.absent(),
    this.mobile = const Value.absent(),
    this.address = const Value.absent(),
    this.skill = const Value.absent(),
    this.photoPath = const Value.absent(),
    this.joinDate = const Value.absent(),
    this.leaveDate = const Value.absent(),
    this.status = const Value.absent(),
    this.note = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  LaboursCompanion.insert({
    required String id,
    required String branchId,
    required String name,
    this.fatherName = const Value.absent(),
    this.mobile = const Value.absent(),
    this.address = const Value.absent(),
    this.skill = const Value.absent(),
    this.photoPath = const Value.absent(),
    required String joinDate,
    this.leaveDate = const Value.absent(),
    this.status = const Value.absent(),
    this.note = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       branchId = Value(branchId),
       name = Value(name),
       joinDate = Value(joinDate);
  static Insertable<Labour> custom({
    Expression<String>? id,
    Expression<String>? branchId,
    Expression<String>? name,
    Expression<String>? fatherName,
    Expression<String>? mobile,
    Expression<String>? address,
    Expression<String>? skill,
    Expression<String>? photoPath,
    Expression<String>? joinDate,
    Expression<String>? leaveDate,
    Expression<String>? status,
    Expression<String>? note,
    Expression<DateTime>? createdAt,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (branchId != null) 'branch_id': branchId,
      if (name != null) 'name': name,
      if (fatherName != null) 'father_name': fatherName,
      if (mobile != null) 'mobile': mobile,
      if (address != null) 'address': address,
      if (skill != null) 'skill': skill,
      if (photoPath != null) 'photo_path': photoPath,
      if (joinDate != null) 'join_date': joinDate,
      if (leaveDate != null) 'leave_date': leaveDate,
      if (status != null) 'status': status,
      if (note != null) 'note': note,
      if (createdAt != null) 'created_at': createdAt,
      if (rowid != null) 'rowid': rowid,
    });
  }

  LaboursCompanion copyWith({
    Value<String>? id,
    Value<String>? branchId,
    Value<String>? name,
    Value<String>? fatherName,
    Value<String>? mobile,
    Value<String>? address,
    Value<String>? skill,
    Value<String?>? photoPath,
    Value<String>? joinDate,
    Value<String?>? leaveDate,
    Value<String>? status,
    Value<String>? note,
    Value<DateTime>? createdAt,
    Value<int>? rowid,
  }) {
    return LaboursCompanion(
      id: id ?? this.id,
      branchId: branchId ?? this.branchId,
      name: name ?? this.name,
      fatherName: fatherName ?? this.fatherName,
      mobile: mobile ?? this.mobile,
      address: address ?? this.address,
      skill: skill ?? this.skill,
      photoPath: photoPath ?? this.photoPath,
      joinDate: joinDate ?? this.joinDate,
      leaveDate: leaveDate ?? this.leaveDate,
      status: status ?? this.status,
      note: note ?? this.note,
      createdAt: createdAt ?? this.createdAt,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (branchId.present) {
      map['branch_id'] = Variable<String>(branchId.value);
    }
    if (name.present) {
      map['name'] = Variable<String>(name.value);
    }
    if (fatherName.present) {
      map['father_name'] = Variable<String>(fatherName.value);
    }
    if (mobile.present) {
      map['mobile'] = Variable<String>(mobile.value);
    }
    if (address.present) {
      map['address'] = Variable<String>(address.value);
    }
    if (skill.present) {
      map['skill'] = Variable<String>(skill.value);
    }
    if (photoPath.present) {
      map['photo_path'] = Variable<String>(photoPath.value);
    }
    if (joinDate.present) {
      map['join_date'] = Variable<String>(joinDate.value);
    }
    if (leaveDate.present) {
      map['leave_date'] = Variable<String>(leaveDate.value);
    }
    if (status.present) {
      map['status'] = Variable<String>(status.value);
    }
    if (note.present) {
      map['note'] = Variable<String>(note.value);
    }
    if (createdAt.present) {
      map['created_at'] = Variable<DateTime>(createdAt.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('LaboursCompanion(')
          ..write('id: $id, ')
          ..write('branchId: $branchId, ')
          ..write('name: $name, ')
          ..write('fatherName: $fatherName, ')
          ..write('mobile: $mobile, ')
          ..write('address: $address, ')
          ..write('skill: $skill, ')
          ..write('photoPath: $photoPath, ')
          ..write('joinDate: $joinDate, ')
          ..write('leaveDate: $leaveDate, ')
          ..write('status: $status, ')
          ..write('note: $note, ')
          ..write('createdAt: $createdAt, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $LabourRatesTable extends LabourRates
    with TableInfo<$LabourRatesTable, LabourRate> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $LabourRatesTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _labourIdMeta = const VerificationMeta(
    'labourId',
  );
  @override
  late final GeneratedColumn<String> labourId = GeneratedColumn<String>(
    'labour_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES labours (id)',
    ),
  );
  static const VerificationMeta _effectiveFromMeta = const VerificationMeta(
    'effectiveFrom',
  );
  @override
  late final GeneratedColumn<String> effectiveFrom = GeneratedColumn<String>(
    'effective_from',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _payTypeMeta = const VerificationMeta(
    'payType',
  );
  @override
  late final GeneratedColumn<String> payType = GeneratedColumn<String>(
    'pay_type',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant('daily'),
  );
  static const VerificationMeta _amountMeta = const VerificationMeta('amount');
  @override
  late final GeneratedColumn<int> amount = GeneratedColumn<int>(
    'amount',
    aliasedName,
    false,
    type: DriftSqlType.int,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _otPerHourMeta = const VerificationMeta(
    'otPerHour',
  );
  @override
  late final GeneratedColumn<int> otPerHour = GeneratedColumn<int>(
    'ot_per_hour',
    aliasedName,
    true,
    type: DriftSqlType.int,
    requiredDuringInsert: false,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    labourId,
    effectiveFrom,
    payType,
    amount,
    otPerHour,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'labour_rates';
  @override
  VerificationContext validateIntegrity(
    Insertable<LabourRate> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('labour_id')) {
      context.handle(
        _labourIdMeta,
        labourId.isAcceptableOrUnknown(data['labour_id']!, _labourIdMeta),
      );
    } else if (isInserting) {
      context.missing(_labourIdMeta);
    }
    if (data.containsKey('effective_from')) {
      context.handle(
        _effectiveFromMeta,
        effectiveFrom.isAcceptableOrUnknown(
          data['effective_from']!,
          _effectiveFromMeta,
        ),
      );
    } else if (isInserting) {
      context.missing(_effectiveFromMeta);
    }
    if (data.containsKey('pay_type')) {
      context.handle(
        _payTypeMeta,
        payType.isAcceptableOrUnknown(data['pay_type']!, _payTypeMeta),
      );
    }
    if (data.containsKey('amount')) {
      context.handle(
        _amountMeta,
        amount.isAcceptableOrUnknown(data['amount']!, _amountMeta),
      );
    } else if (isInserting) {
      context.missing(_amountMeta);
    }
    if (data.containsKey('ot_per_hour')) {
      context.handle(
        _otPerHourMeta,
        otPerHour.isAcceptableOrUnknown(data['ot_per_hour']!, _otPerHourMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  LabourRate map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return LabourRate(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      labourId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}labour_id'],
      )!,
      effectiveFrom: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}effective_from'],
      )!,
      payType: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}pay_type'],
      )!,
      amount: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}amount'],
      )!,
      otPerHour: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}ot_per_hour'],
      ),
    );
  }

  @override
  $LabourRatesTable createAlias(String alias) {
    return $LabourRatesTable(attachedDatabase, alias);
  }
}

class LabourRate extends DataClass implements Insertable<LabourRate> {
  final String id;
  final String labourId;
  final String effectiveFrom;

  /// daily | monthly
  final String payType;
  final int amount;

  /// Overtime paise per hour. Null = derived from the daily rate.
  final int? otPerHour;
  const LabourRate({
    required this.id,
    required this.labourId,
    required this.effectiveFrom,
    required this.payType,
    required this.amount,
    this.otPerHour,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['labour_id'] = Variable<String>(labourId);
    map['effective_from'] = Variable<String>(effectiveFrom);
    map['pay_type'] = Variable<String>(payType);
    map['amount'] = Variable<int>(amount);
    if (!nullToAbsent || otPerHour != null) {
      map['ot_per_hour'] = Variable<int>(otPerHour);
    }
    return map;
  }

  LabourRatesCompanion toCompanion(bool nullToAbsent) {
    return LabourRatesCompanion(
      id: Value(id),
      labourId: Value(labourId),
      effectiveFrom: Value(effectiveFrom),
      payType: Value(payType),
      amount: Value(amount),
      otPerHour: otPerHour == null && nullToAbsent
          ? const Value.absent()
          : Value(otPerHour),
    );
  }

  factory LabourRate.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return LabourRate(
      id: serializer.fromJson<String>(json['id']),
      labourId: serializer.fromJson<String>(json['labourId']),
      effectiveFrom: serializer.fromJson<String>(json['effectiveFrom']),
      payType: serializer.fromJson<String>(json['payType']),
      amount: serializer.fromJson<int>(json['amount']),
      otPerHour: serializer.fromJson<int?>(json['otPerHour']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'labourId': serializer.toJson<String>(labourId),
      'effectiveFrom': serializer.toJson<String>(effectiveFrom),
      'payType': serializer.toJson<String>(payType),
      'amount': serializer.toJson<int>(amount),
      'otPerHour': serializer.toJson<int?>(otPerHour),
    };
  }

  LabourRate copyWith({
    String? id,
    String? labourId,
    String? effectiveFrom,
    String? payType,
    int? amount,
    Value<int?> otPerHour = const Value.absent(),
  }) => LabourRate(
    id: id ?? this.id,
    labourId: labourId ?? this.labourId,
    effectiveFrom: effectiveFrom ?? this.effectiveFrom,
    payType: payType ?? this.payType,
    amount: amount ?? this.amount,
    otPerHour: otPerHour.present ? otPerHour.value : this.otPerHour,
  );
  LabourRate copyWithCompanion(LabourRatesCompanion data) {
    return LabourRate(
      id: data.id.present ? data.id.value : this.id,
      labourId: data.labourId.present ? data.labourId.value : this.labourId,
      effectiveFrom: data.effectiveFrom.present
          ? data.effectiveFrom.value
          : this.effectiveFrom,
      payType: data.payType.present ? data.payType.value : this.payType,
      amount: data.amount.present ? data.amount.value : this.amount,
      otPerHour: data.otPerHour.present ? data.otPerHour.value : this.otPerHour,
    );
  }

  @override
  String toString() {
    return (StringBuffer('LabourRate(')
          ..write('id: $id, ')
          ..write('labourId: $labourId, ')
          ..write('effectiveFrom: $effectiveFrom, ')
          ..write('payType: $payType, ')
          ..write('amount: $amount, ')
          ..write('otPerHour: $otPerHour')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode =>
      Object.hash(id, labourId, effectiveFrom, payType, amount, otPerHour);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is LabourRate &&
          other.id == this.id &&
          other.labourId == this.labourId &&
          other.effectiveFrom == this.effectiveFrom &&
          other.payType == this.payType &&
          other.amount == this.amount &&
          other.otPerHour == this.otPerHour);
}

class LabourRatesCompanion extends UpdateCompanion<LabourRate> {
  final Value<String> id;
  final Value<String> labourId;
  final Value<String> effectiveFrom;
  final Value<String> payType;
  final Value<int> amount;
  final Value<int?> otPerHour;
  final Value<int> rowid;
  const LabourRatesCompanion({
    this.id = const Value.absent(),
    this.labourId = const Value.absent(),
    this.effectiveFrom = const Value.absent(),
    this.payType = const Value.absent(),
    this.amount = const Value.absent(),
    this.otPerHour = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  LabourRatesCompanion.insert({
    required String id,
    required String labourId,
    required String effectiveFrom,
    this.payType = const Value.absent(),
    required int amount,
    this.otPerHour = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       labourId = Value(labourId),
       effectiveFrom = Value(effectiveFrom),
       amount = Value(amount);
  static Insertable<LabourRate> custom({
    Expression<String>? id,
    Expression<String>? labourId,
    Expression<String>? effectiveFrom,
    Expression<String>? payType,
    Expression<int>? amount,
    Expression<int>? otPerHour,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (labourId != null) 'labour_id': labourId,
      if (effectiveFrom != null) 'effective_from': effectiveFrom,
      if (payType != null) 'pay_type': payType,
      if (amount != null) 'amount': amount,
      if (otPerHour != null) 'ot_per_hour': otPerHour,
      if (rowid != null) 'rowid': rowid,
    });
  }

  LabourRatesCompanion copyWith({
    Value<String>? id,
    Value<String>? labourId,
    Value<String>? effectiveFrom,
    Value<String>? payType,
    Value<int>? amount,
    Value<int?>? otPerHour,
    Value<int>? rowid,
  }) {
    return LabourRatesCompanion(
      id: id ?? this.id,
      labourId: labourId ?? this.labourId,
      effectiveFrom: effectiveFrom ?? this.effectiveFrom,
      payType: payType ?? this.payType,
      amount: amount ?? this.amount,
      otPerHour: otPerHour ?? this.otPerHour,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (labourId.present) {
      map['labour_id'] = Variable<String>(labourId.value);
    }
    if (effectiveFrom.present) {
      map['effective_from'] = Variable<String>(effectiveFrom.value);
    }
    if (payType.present) {
      map['pay_type'] = Variable<String>(payType.value);
    }
    if (amount.present) {
      map['amount'] = Variable<int>(amount.value);
    }
    if (otPerHour.present) {
      map['ot_per_hour'] = Variable<int>(otPerHour.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('LabourRatesCompanion(')
          ..write('id: $id, ')
          ..write('labourId: $labourId, ')
          ..write('effectiveFrom: $effectiveFrom, ')
          ..write('payType: $payType, ')
          ..write('amount: $amount, ')
          ..write('otPerHour: $otPerHour, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $LabourDocumentsTable extends LabourDocuments
    with TableInfo<$LabourDocumentsTable, LabourDocument> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $LabourDocumentsTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _labourIdMeta = const VerificationMeta(
    'labourId',
  );
  @override
  late final GeneratedColumn<String> labourId = GeneratedColumn<String>(
    'labour_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES labours (id)',
    ),
  );
  static const VerificationMeta _docTypeMeta = const VerificationMeta(
    'docType',
  );
  @override
  late final GeneratedColumn<String> docType = GeneratedColumn<String>(
    'doc_type',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _docNumberMeta = const VerificationMeta(
    'docNumber',
  );
  @override
  late final GeneratedColumn<String> docNumber = GeneratedColumn<String>(
    'doc_number',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _filePathMeta = const VerificationMeta(
    'filePath',
  );
  @override
  late final GeneratedColumn<String> filePath = GeneratedColumn<String>(
    'file_path',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _expiryDateMeta = const VerificationMeta(
    'expiryDate',
  );
  @override
  late final GeneratedColumn<String> expiryDate = GeneratedColumn<String>(
    'expiry_date',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _createdAtMeta = const VerificationMeta(
    'createdAt',
  );
  @override
  late final GeneratedColumn<DateTime> createdAt = GeneratedColumn<DateTime>(
    'created_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    labourId,
    docType,
    docNumber,
    filePath,
    expiryDate,
    createdAt,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'labour_documents';
  @override
  VerificationContext validateIntegrity(
    Insertable<LabourDocument> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('labour_id')) {
      context.handle(
        _labourIdMeta,
        labourId.isAcceptableOrUnknown(data['labour_id']!, _labourIdMeta),
      );
    } else if (isInserting) {
      context.missing(_labourIdMeta);
    }
    if (data.containsKey('doc_type')) {
      context.handle(
        _docTypeMeta,
        docType.isAcceptableOrUnknown(data['doc_type']!, _docTypeMeta),
      );
    } else if (isInserting) {
      context.missing(_docTypeMeta);
    }
    if (data.containsKey('doc_number')) {
      context.handle(
        _docNumberMeta,
        docNumber.isAcceptableOrUnknown(data['doc_number']!, _docNumberMeta),
      );
    }
    if (data.containsKey('file_path')) {
      context.handle(
        _filePathMeta,
        filePath.isAcceptableOrUnknown(data['file_path']!, _filePathMeta),
      );
    }
    if (data.containsKey('expiry_date')) {
      context.handle(
        _expiryDateMeta,
        expiryDate.isAcceptableOrUnknown(data['expiry_date']!, _expiryDateMeta),
      );
    }
    if (data.containsKey('created_at')) {
      context.handle(
        _createdAtMeta,
        createdAt.isAcceptableOrUnknown(data['created_at']!, _createdAtMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  LabourDocument map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return LabourDocument(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      labourId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}labour_id'],
      )!,
      docType: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}doc_type'],
      )!,
      docNumber: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}doc_number'],
      )!,
      filePath: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}file_path'],
      ),
      expiryDate: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}expiry_date'],
      ),
      createdAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}created_at'],
      )!,
    );
  }

  @override
  $LabourDocumentsTable createAlias(String alias) {
    return $LabourDocumentsTable(attachedDatabase, alias);
  }
}

class LabourDocument extends DataClass implements Insertable<LabourDocument> {
  final String id;
  final String labourId;
  final String docType;
  final String docNumber;
  final String? filePath;
  final String? expiryDate;
  final DateTime createdAt;
  const LabourDocument({
    required this.id,
    required this.labourId,
    required this.docType,
    required this.docNumber,
    this.filePath,
    this.expiryDate,
    required this.createdAt,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['labour_id'] = Variable<String>(labourId);
    map['doc_type'] = Variable<String>(docType);
    map['doc_number'] = Variable<String>(docNumber);
    if (!nullToAbsent || filePath != null) {
      map['file_path'] = Variable<String>(filePath);
    }
    if (!nullToAbsent || expiryDate != null) {
      map['expiry_date'] = Variable<String>(expiryDate);
    }
    map['created_at'] = Variable<DateTime>(createdAt);
    return map;
  }

  LabourDocumentsCompanion toCompanion(bool nullToAbsent) {
    return LabourDocumentsCompanion(
      id: Value(id),
      labourId: Value(labourId),
      docType: Value(docType),
      docNumber: Value(docNumber),
      filePath: filePath == null && nullToAbsent
          ? const Value.absent()
          : Value(filePath),
      expiryDate: expiryDate == null && nullToAbsent
          ? const Value.absent()
          : Value(expiryDate),
      createdAt: Value(createdAt),
    );
  }

  factory LabourDocument.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return LabourDocument(
      id: serializer.fromJson<String>(json['id']),
      labourId: serializer.fromJson<String>(json['labourId']),
      docType: serializer.fromJson<String>(json['docType']),
      docNumber: serializer.fromJson<String>(json['docNumber']),
      filePath: serializer.fromJson<String?>(json['filePath']),
      expiryDate: serializer.fromJson<String?>(json['expiryDate']),
      createdAt: serializer.fromJson<DateTime>(json['createdAt']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'labourId': serializer.toJson<String>(labourId),
      'docType': serializer.toJson<String>(docType),
      'docNumber': serializer.toJson<String>(docNumber),
      'filePath': serializer.toJson<String?>(filePath),
      'expiryDate': serializer.toJson<String?>(expiryDate),
      'createdAt': serializer.toJson<DateTime>(createdAt),
    };
  }

  LabourDocument copyWith({
    String? id,
    String? labourId,
    String? docType,
    String? docNumber,
    Value<String?> filePath = const Value.absent(),
    Value<String?> expiryDate = const Value.absent(),
    DateTime? createdAt,
  }) => LabourDocument(
    id: id ?? this.id,
    labourId: labourId ?? this.labourId,
    docType: docType ?? this.docType,
    docNumber: docNumber ?? this.docNumber,
    filePath: filePath.present ? filePath.value : this.filePath,
    expiryDate: expiryDate.present ? expiryDate.value : this.expiryDate,
    createdAt: createdAt ?? this.createdAt,
  );
  LabourDocument copyWithCompanion(LabourDocumentsCompanion data) {
    return LabourDocument(
      id: data.id.present ? data.id.value : this.id,
      labourId: data.labourId.present ? data.labourId.value : this.labourId,
      docType: data.docType.present ? data.docType.value : this.docType,
      docNumber: data.docNumber.present ? data.docNumber.value : this.docNumber,
      filePath: data.filePath.present ? data.filePath.value : this.filePath,
      expiryDate: data.expiryDate.present
          ? data.expiryDate.value
          : this.expiryDate,
      createdAt: data.createdAt.present ? data.createdAt.value : this.createdAt,
    );
  }

  @override
  String toString() {
    return (StringBuffer('LabourDocument(')
          ..write('id: $id, ')
          ..write('labourId: $labourId, ')
          ..write('docType: $docType, ')
          ..write('docNumber: $docNumber, ')
          ..write('filePath: $filePath, ')
          ..write('expiryDate: $expiryDate, ')
          ..write('createdAt: $createdAt')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    id,
    labourId,
    docType,
    docNumber,
    filePath,
    expiryDate,
    createdAt,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is LabourDocument &&
          other.id == this.id &&
          other.labourId == this.labourId &&
          other.docType == this.docType &&
          other.docNumber == this.docNumber &&
          other.filePath == this.filePath &&
          other.expiryDate == this.expiryDate &&
          other.createdAt == this.createdAt);
}

class LabourDocumentsCompanion extends UpdateCompanion<LabourDocument> {
  final Value<String> id;
  final Value<String> labourId;
  final Value<String> docType;
  final Value<String> docNumber;
  final Value<String?> filePath;
  final Value<String?> expiryDate;
  final Value<DateTime> createdAt;
  final Value<int> rowid;
  const LabourDocumentsCompanion({
    this.id = const Value.absent(),
    this.labourId = const Value.absent(),
    this.docType = const Value.absent(),
    this.docNumber = const Value.absent(),
    this.filePath = const Value.absent(),
    this.expiryDate = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  LabourDocumentsCompanion.insert({
    required String id,
    required String labourId,
    required String docType,
    this.docNumber = const Value.absent(),
    this.filePath = const Value.absent(),
    this.expiryDate = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       labourId = Value(labourId),
       docType = Value(docType);
  static Insertable<LabourDocument> custom({
    Expression<String>? id,
    Expression<String>? labourId,
    Expression<String>? docType,
    Expression<String>? docNumber,
    Expression<String>? filePath,
    Expression<String>? expiryDate,
    Expression<DateTime>? createdAt,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (labourId != null) 'labour_id': labourId,
      if (docType != null) 'doc_type': docType,
      if (docNumber != null) 'doc_number': docNumber,
      if (filePath != null) 'file_path': filePath,
      if (expiryDate != null) 'expiry_date': expiryDate,
      if (createdAt != null) 'created_at': createdAt,
      if (rowid != null) 'rowid': rowid,
    });
  }

  LabourDocumentsCompanion copyWith({
    Value<String>? id,
    Value<String>? labourId,
    Value<String>? docType,
    Value<String>? docNumber,
    Value<String?>? filePath,
    Value<String?>? expiryDate,
    Value<DateTime>? createdAt,
    Value<int>? rowid,
  }) {
    return LabourDocumentsCompanion(
      id: id ?? this.id,
      labourId: labourId ?? this.labourId,
      docType: docType ?? this.docType,
      docNumber: docNumber ?? this.docNumber,
      filePath: filePath ?? this.filePath,
      expiryDate: expiryDate ?? this.expiryDate,
      createdAt: createdAt ?? this.createdAt,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (labourId.present) {
      map['labour_id'] = Variable<String>(labourId.value);
    }
    if (docType.present) {
      map['doc_type'] = Variable<String>(docType.value);
    }
    if (docNumber.present) {
      map['doc_number'] = Variable<String>(docNumber.value);
    }
    if (filePath.present) {
      map['file_path'] = Variable<String>(filePath.value);
    }
    if (expiryDate.present) {
      map['expiry_date'] = Variable<String>(expiryDate.value);
    }
    if (createdAt.present) {
      map['created_at'] = Variable<DateTime>(createdAt.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('LabourDocumentsCompanion(')
          ..write('id: $id, ')
          ..write('labourId: $labourId, ')
          ..write('docType: $docType, ')
          ..write('docNumber: $docNumber, ')
          ..write('filePath: $filePath, ')
          ..write('expiryDate: $expiryDate, ')
          ..write('createdAt: $createdAt, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $AssignmentsTable extends Assignments
    with TableInfo<$AssignmentsTable, Assignment> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $AssignmentsTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _labourIdMeta = const VerificationMeta(
    'labourId',
  );
  @override
  late final GeneratedColumn<String> labourId = GeneratedColumn<String>(
    'labour_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES labours (id)',
    ),
  );
  static const VerificationMeta _siteIdMeta = const VerificationMeta('siteId');
  @override
  late final GeneratedColumn<String> siteId = GeneratedColumn<String>(
    'site_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES sites (id)',
    ),
  );
  static const VerificationMeta _fromDateMeta = const VerificationMeta(
    'fromDate',
  );
  @override
  late final GeneratedColumn<String> fromDate = GeneratedColumn<String>(
    'from_date',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _toDateMeta = const VerificationMeta('toDate');
  @override
  late final GeneratedColumn<String> toDate = GeneratedColumn<String>(
    'to_date',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    labourId,
    siteId,
    fromDate,
    toDate,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'assignments';
  @override
  VerificationContext validateIntegrity(
    Insertable<Assignment> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('labour_id')) {
      context.handle(
        _labourIdMeta,
        labourId.isAcceptableOrUnknown(data['labour_id']!, _labourIdMeta),
      );
    } else if (isInserting) {
      context.missing(_labourIdMeta);
    }
    if (data.containsKey('site_id')) {
      context.handle(
        _siteIdMeta,
        siteId.isAcceptableOrUnknown(data['site_id']!, _siteIdMeta),
      );
    } else if (isInserting) {
      context.missing(_siteIdMeta);
    }
    if (data.containsKey('from_date')) {
      context.handle(
        _fromDateMeta,
        fromDate.isAcceptableOrUnknown(data['from_date']!, _fromDateMeta),
      );
    } else if (isInserting) {
      context.missing(_fromDateMeta);
    }
    if (data.containsKey('to_date')) {
      context.handle(
        _toDateMeta,
        toDate.isAcceptableOrUnknown(data['to_date']!, _toDateMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  Assignment map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return Assignment(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      labourId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}labour_id'],
      )!,
      siteId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}site_id'],
      )!,
      fromDate: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}from_date'],
      )!,
      toDate: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}to_date'],
      ),
    );
  }

  @override
  $AssignmentsTable createAlias(String alias) {
    return $AssignmentsTable(attachedDatabase, alias);
  }
}

class Assignment extends DataClass implements Insertable<Assignment> {
  final String id;
  final String labourId;
  final String siteId;
  final String fromDate;
  final String? toDate;
  const Assignment({
    required this.id,
    required this.labourId,
    required this.siteId,
    required this.fromDate,
    this.toDate,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['labour_id'] = Variable<String>(labourId);
    map['site_id'] = Variable<String>(siteId);
    map['from_date'] = Variable<String>(fromDate);
    if (!nullToAbsent || toDate != null) {
      map['to_date'] = Variable<String>(toDate);
    }
    return map;
  }

  AssignmentsCompanion toCompanion(bool nullToAbsent) {
    return AssignmentsCompanion(
      id: Value(id),
      labourId: Value(labourId),
      siteId: Value(siteId),
      fromDate: Value(fromDate),
      toDate: toDate == null && nullToAbsent
          ? const Value.absent()
          : Value(toDate),
    );
  }

  factory Assignment.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return Assignment(
      id: serializer.fromJson<String>(json['id']),
      labourId: serializer.fromJson<String>(json['labourId']),
      siteId: serializer.fromJson<String>(json['siteId']),
      fromDate: serializer.fromJson<String>(json['fromDate']),
      toDate: serializer.fromJson<String?>(json['toDate']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'labourId': serializer.toJson<String>(labourId),
      'siteId': serializer.toJson<String>(siteId),
      'fromDate': serializer.toJson<String>(fromDate),
      'toDate': serializer.toJson<String?>(toDate),
    };
  }

  Assignment copyWith({
    String? id,
    String? labourId,
    String? siteId,
    String? fromDate,
    Value<String?> toDate = const Value.absent(),
  }) => Assignment(
    id: id ?? this.id,
    labourId: labourId ?? this.labourId,
    siteId: siteId ?? this.siteId,
    fromDate: fromDate ?? this.fromDate,
    toDate: toDate.present ? toDate.value : this.toDate,
  );
  Assignment copyWithCompanion(AssignmentsCompanion data) {
    return Assignment(
      id: data.id.present ? data.id.value : this.id,
      labourId: data.labourId.present ? data.labourId.value : this.labourId,
      siteId: data.siteId.present ? data.siteId.value : this.siteId,
      fromDate: data.fromDate.present ? data.fromDate.value : this.fromDate,
      toDate: data.toDate.present ? data.toDate.value : this.toDate,
    );
  }

  @override
  String toString() {
    return (StringBuffer('Assignment(')
          ..write('id: $id, ')
          ..write('labourId: $labourId, ')
          ..write('siteId: $siteId, ')
          ..write('fromDate: $fromDate, ')
          ..write('toDate: $toDate')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(id, labourId, siteId, fromDate, toDate);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is Assignment &&
          other.id == this.id &&
          other.labourId == this.labourId &&
          other.siteId == this.siteId &&
          other.fromDate == this.fromDate &&
          other.toDate == this.toDate);
}

class AssignmentsCompanion extends UpdateCompanion<Assignment> {
  final Value<String> id;
  final Value<String> labourId;
  final Value<String> siteId;
  final Value<String> fromDate;
  final Value<String?> toDate;
  final Value<int> rowid;
  const AssignmentsCompanion({
    this.id = const Value.absent(),
    this.labourId = const Value.absent(),
    this.siteId = const Value.absent(),
    this.fromDate = const Value.absent(),
    this.toDate = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  AssignmentsCompanion.insert({
    required String id,
    required String labourId,
    required String siteId,
    required String fromDate,
    this.toDate = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       labourId = Value(labourId),
       siteId = Value(siteId),
       fromDate = Value(fromDate);
  static Insertable<Assignment> custom({
    Expression<String>? id,
    Expression<String>? labourId,
    Expression<String>? siteId,
    Expression<String>? fromDate,
    Expression<String>? toDate,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (labourId != null) 'labour_id': labourId,
      if (siteId != null) 'site_id': siteId,
      if (fromDate != null) 'from_date': fromDate,
      if (toDate != null) 'to_date': toDate,
      if (rowid != null) 'rowid': rowid,
    });
  }

  AssignmentsCompanion copyWith({
    Value<String>? id,
    Value<String>? labourId,
    Value<String>? siteId,
    Value<String>? fromDate,
    Value<String?>? toDate,
    Value<int>? rowid,
  }) {
    return AssignmentsCompanion(
      id: id ?? this.id,
      labourId: labourId ?? this.labourId,
      siteId: siteId ?? this.siteId,
      fromDate: fromDate ?? this.fromDate,
      toDate: toDate ?? this.toDate,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (labourId.present) {
      map['labour_id'] = Variable<String>(labourId.value);
    }
    if (siteId.present) {
      map['site_id'] = Variable<String>(siteId.value);
    }
    if (fromDate.present) {
      map['from_date'] = Variable<String>(fromDate.value);
    }
    if (toDate.present) {
      map['to_date'] = Variable<String>(toDate.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('AssignmentsCompanion(')
          ..write('id: $id, ')
          ..write('labourId: $labourId, ')
          ..write('siteId: $siteId, ')
          ..write('fromDate: $fromDate, ')
          ..write('toDate: $toDate, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $AttendanceTable extends Attendance
    with TableInfo<$AttendanceTable, AttendanceData> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $AttendanceTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _labourIdMeta = const VerificationMeta(
    'labourId',
  );
  @override
  late final GeneratedColumn<String> labourId = GeneratedColumn<String>(
    'labour_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES labours (id)',
    ),
  );
  static const VerificationMeta _siteIdMeta = const VerificationMeta('siteId');
  @override
  late final GeneratedColumn<String> siteId = GeneratedColumn<String>(
    'site_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES sites (id)',
    ),
  );
  static const VerificationMeta _dateMeta = const VerificationMeta('date');
  @override
  late final GeneratedColumn<String> date = GeneratedColumn<String>(
    'date',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _statusMeta = const VerificationMeta('status');
  @override
  late final GeneratedColumn<String> status = GeneratedColumn<String>(
    'status',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _otHoursMeta = const VerificationMeta(
    'otHours',
  );
  @override
  late final GeneratedColumn<double> otHours = GeneratedColumn<double>(
    'ot_hours',
    aliasedName,
    false,
    type: DriftSqlType.double,
    requiredDuringInsert: false,
    defaultValue: const Constant(0),
  );
  static const VerificationMeta _noteMeta = const VerificationMeta('note');
  @override
  late final GeneratedColumn<String> note = GeneratedColumn<String>(
    'note',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _markedByMeta = const VerificationMeta(
    'markedBy',
  );
  @override
  late final GeneratedColumn<String> markedBy = GeneratedColumn<String>(
    'marked_by',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _markedAtMeta = const VerificationMeta(
    'markedAt',
  );
  @override
  late final GeneratedColumn<DateTime> markedAt = GeneratedColumn<DateTime>(
    'marked_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  static const VerificationMeta _isLateMeta = const VerificationMeta('isLate');
  @override
  late final GeneratedColumn<bool> isLate = GeneratedColumn<bool>(
    'is_late',
    aliasedName,
    false,
    type: DriftSqlType.bool,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'CHECK ("is_late" IN (0, 1))',
    ),
    defaultValue: const Constant(false),
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    labourId,
    siteId,
    date,
    status,
    otHours,
    note,
    markedBy,
    markedAt,
    isLate,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'attendance';
  @override
  VerificationContext validateIntegrity(
    Insertable<AttendanceData> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('labour_id')) {
      context.handle(
        _labourIdMeta,
        labourId.isAcceptableOrUnknown(data['labour_id']!, _labourIdMeta),
      );
    } else if (isInserting) {
      context.missing(_labourIdMeta);
    }
    if (data.containsKey('site_id')) {
      context.handle(
        _siteIdMeta,
        siteId.isAcceptableOrUnknown(data['site_id']!, _siteIdMeta),
      );
    } else if (isInserting) {
      context.missing(_siteIdMeta);
    }
    if (data.containsKey('date')) {
      context.handle(
        _dateMeta,
        date.isAcceptableOrUnknown(data['date']!, _dateMeta),
      );
    } else if (isInserting) {
      context.missing(_dateMeta);
    }
    if (data.containsKey('status')) {
      context.handle(
        _statusMeta,
        status.isAcceptableOrUnknown(data['status']!, _statusMeta),
      );
    } else if (isInserting) {
      context.missing(_statusMeta);
    }
    if (data.containsKey('ot_hours')) {
      context.handle(
        _otHoursMeta,
        otHours.isAcceptableOrUnknown(data['ot_hours']!, _otHoursMeta),
      );
    }
    if (data.containsKey('note')) {
      context.handle(
        _noteMeta,
        note.isAcceptableOrUnknown(data['note']!, _noteMeta),
      );
    }
    if (data.containsKey('marked_by')) {
      context.handle(
        _markedByMeta,
        markedBy.isAcceptableOrUnknown(data['marked_by']!, _markedByMeta),
      );
    }
    if (data.containsKey('marked_at')) {
      context.handle(
        _markedAtMeta,
        markedAt.isAcceptableOrUnknown(data['marked_at']!, _markedAtMeta),
      );
    }
    if (data.containsKey('is_late')) {
      context.handle(
        _isLateMeta,
        isLate.isAcceptableOrUnknown(data['is_late']!, _isLateMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  List<Set<GeneratedColumn>> get uniqueKeys => [
    {labourId, siteId, date},
  ];
  @override
  AttendanceData map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return AttendanceData(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      labourId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}labour_id'],
      )!,
      siteId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}site_id'],
      )!,
      date: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}date'],
      )!,
      status: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}status'],
      )!,
      otHours: attachedDatabase.typeMapping.read(
        DriftSqlType.double,
        data['${effectivePrefix}ot_hours'],
      )!,
      note: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}note'],
      )!,
      markedBy: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}marked_by'],
      ),
      markedAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}marked_at'],
      )!,
      isLate: attachedDatabase.typeMapping.read(
        DriftSqlType.bool,
        data['${effectivePrefix}is_late'],
      )!,
    );
  }

  @override
  $AttendanceTable createAlias(String alias) {
    return $AttendanceTable(attachedDatabase, alias);
  }
}

class AttendanceData extends DataClass implements Insertable<AttendanceData> {
  final String id;
  final String labourId;
  final String siteId;
  final String date;

  /// P | A | H
  final String status;
  final double otHours;
  final String note;
  final String? markedBy;
  final DateTime markedAt;
  final bool isLate;
  const AttendanceData({
    required this.id,
    required this.labourId,
    required this.siteId,
    required this.date,
    required this.status,
    required this.otHours,
    required this.note,
    this.markedBy,
    required this.markedAt,
    required this.isLate,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['labour_id'] = Variable<String>(labourId);
    map['site_id'] = Variable<String>(siteId);
    map['date'] = Variable<String>(date);
    map['status'] = Variable<String>(status);
    map['ot_hours'] = Variable<double>(otHours);
    map['note'] = Variable<String>(note);
    if (!nullToAbsent || markedBy != null) {
      map['marked_by'] = Variable<String>(markedBy);
    }
    map['marked_at'] = Variable<DateTime>(markedAt);
    map['is_late'] = Variable<bool>(isLate);
    return map;
  }

  AttendanceCompanion toCompanion(bool nullToAbsent) {
    return AttendanceCompanion(
      id: Value(id),
      labourId: Value(labourId),
      siteId: Value(siteId),
      date: Value(date),
      status: Value(status),
      otHours: Value(otHours),
      note: Value(note),
      markedBy: markedBy == null && nullToAbsent
          ? const Value.absent()
          : Value(markedBy),
      markedAt: Value(markedAt),
      isLate: Value(isLate),
    );
  }

  factory AttendanceData.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return AttendanceData(
      id: serializer.fromJson<String>(json['id']),
      labourId: serializer.fromJson<String>(json['labourId']),
      siteId: serializer.fromJson<String>(json['siteId']),
      date: serializer.fromJson<String>(json['date']),
      status: serializer.fromJson<String>(json['status']),
      otHours: serializer.fromJson<double>(json['otHours']),
      note: serializer.fromJson<String>(json['note']),
      markedBy: serializer.fromJson<String?>(json['markedBy']),
      markedAt: serializer.fromJson<DateTime>(json['markedAt']),
      isLate: serializer.fromJson<bool>(json['isLate']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'labourId': serializer.toJson<String>(labourId),
      'siteId': serializer.toJson<String>(siteId),
      'date': serializer.toJson<String>(date),
      'status': serializer.toJson<String>(status),
      'otHours': serializer.toJson<double>(otHours),
      'note': serializer.toJson<String>(note),
      'markedBy': serializer.toJson<String?>(markedBy),
      'markedAt': serializer.toJson<DateTime>(markedAt),
      'isLate': serializer.toJson<bool>(isLate),
    };
  }

  AttendanceData copyWith({
    String? id,
    String? labourId,
    String? siteId,
    String? date,
    String? status,
    double? otHours,
    String? note,
    Value<String?> markedBy = const Value.absent(),
    DateTime? markedAt,
    bool? isLate,
  }) => AttendanceData(
    id: id ?? this.id,
    labourId: labourId ?? this.labourId,
    siteId: siteId ?? this.siteId,
    date: date ?? this.date,
    status: status ?? this.status,
    otHours: otHours ?? this.otHours,
    note: note ?? this.note,
    markedBy: markedBy.present ? markedBy.value : this.markedBy,
    markedAt: markedAt ?? this.markedAt,
    isLate: isLate ?? this.isLate,
  );
  AttendanceData copyWithCompanion(AttendanceCompanion data) {
    return AttendanceData(
      id: data.id.present ? data.id.value : this.id,
      labourId: data.labourId.present ? data.labourId.value : this.labourId,
      siteId: data.siteId.present ? data.siteId.value : this.siteId,
      date: data.date.present ? data.date.value : this.date,
      status: data.status.present ? data.status.value : this.status,
      otHours: data.otHours.present ? data.otHours.value : this.otHours,
      note: data.note.present ? data.note.value : this.note,
      markedBy: data.markedBy.present ? data.markedBy.value : this.markedBy,
      markedAt: data.markedAt.present ? data.markedAt.value : this.markedAt,
      isLate: data.isLate.present ? data.isLate.value : this.isLate,
    );
  }

  @override
  String toString() {
    return (StringBuffer('AttendanceData(')
          ..write('id: $id, ')
          ..write('labourId: $labourId, ')
          ..write('siteId: $siteId, ')
          ..write('date: $date, ')
          ..write('status: $status, ')
          ..write('otHours: $otHours, ')
          ..write('note: $note, ')
          ..write('markedBy: $markedBy, ')
          ..write('markedAt: $markedAt, ')
          ..write('isLate: $isLate')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    id,
    labourId,
    siteId,
    date,
    status,
    otHours,
    note,
    markedBy,
    markedAt,
    isLate,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is AttendanceData &&
          other.id == this.id &&
          other.labourId == this.labourId &&
          other.siteId == this.siteId &&
          other.date == this.date &&
          other.status == this.status &&
          other.otHours == this.otHours &&
          other.note == this.note &&
          other.markedBy == this.markedBy &&
          other.markedAt == this.markedAt &&
          other.isLate == this.isLate);
}

class AttendanceCompanion extends UpdateCompanion<AttendanceData> {
  final Value<String> id;
  final Value<String> labourId;
  final Value<String> siteId;
  final Value<String> date;
  final Value<String> status;
  final Value<double> otHours;
  final Value<String> note;
  final Value<String?> markedBy;
  final Value<DateTime> markedAt;
  final Value<bool> isLate;
  final Value<int> rowid;
  const AttendanceCompanion({
    this.id = const Value.absent(),
    this.labourId = const Value.absent(),
    this.siteId = const Value.absent(),
    this.date = const Value.absent(),
    this.status = const Value.absent(),
    this.otHours = const Value.absent(),
    this.note = const Value.absent(),
    this.markedBy = const Value.absent(),
    this.markedAt = const Value.absent(),
    this.isLate = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  AttendanceCompanion.insert({
    required String id,
    required String labourId,
    required String siteId,
    required String date,
    required String status,
    this.otHours = const Value.absent(),
    this.note = const Value.absent(),
    this.markedBy = const Value.absent(),
    this.markedAt = const Value.absent(),
    this.isLate = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       labourId = Value(labourId),
       siteId = Value(siteId),
       date = Value(date),
       status = Value(status);
  static Insertable<AttendanceData> custom({
    Expression<String>? id,
    Expression<String>? labourId,
    Expression<String>? siteId,
    Expression<String>? date,
    Expression<String>? status,
    Expression<double>? otHours,
    Expression<String>? note,
    Expression<String>? markedBy,
    Expression<DateTime>? markedAt,
    Expression<bool>? isLate,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (labourId != null) 'labour_id': labourId,
      if (siteId != null) 'site_id': siteId,
      if (date != null) 'date': date,
      if (status != null) 'status': status,
      if (otHours != null) 'ot_hours': otHours,
      if (note != null) 'note': note,
      if (markedBy != null) 'marked_by': markedBy,
      if (markedAt != null) 'marked_at': markedAt,
      if (isLate != null) 'is_late': isLate,
      if (rowid != null) 'rowid': rowid,
    });
  }

  AttendanceCompanion copyWith({
    Value<String>? id,
    Value<String>? labourId,
    Value<String>? siteId,
    Value<String>? date,
    Value<String>? status,
    Value<double>? otHours,
    Value<String>? note,
    Value<String?>? markedBy,
    Value<DateTime>? markedAt,
    Value<bool>? isLate,
    Value<int>? rowid,
  }) {
    return AttendanceCompanion(
      id: id ?? this.id,
      labourId: labourId ?? this.labourId,
      siteId: siteId ?? this.siteId,
      date: date ?? this.date,
      status: status ?? this.status,
      otHours: otHours ?? this.otHours,
      note: note ?? this.note,
      markedBy: markedBy ?? this.markedBy,
      markedAt: markedAt ?? this.markedAt,
      isLate: isLate ?? this.isLate,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (labourId.present) {
      map['labour_id'] = Variable<String>(labourId.value);
    }
    if (siteId.present) {
      map['site_id'] = Variable<String>(siteId.value);
    }
    if (date.present) {
      map['date'] = Variable<String>(date.value);
    }
    if (status.present) {
      map['status'] = Variable<String>(status.value);
    }
    if (otHours.present) {
      map['ot_hours'] = Variable<double>(otHours.value);
    }
    if (note.present) {
      map['note'] = Variable<String>(note.value);
    }
    if (markedBy.present) {
      map['marked_by'] = Variable<String>(markedBy.value);
    }
    if (markedAt.present) {
      map['marked_at'] = Variable<DateTime>(markedAt.value);
    }
    if (isLate.present) {
      map['is_late'] = Variable<bool>(isLate.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('AttendanceCompanion(')
          ..write('id: $id, ')
          ..write('labourId: $labourId, ')
          ..write('siteId: $siteId, ')
          ..write('date: $date, ')
          ..write('status: $status, ')
          ..write('otHours: $otHours, ')
          ..write('note: $note, ')
          ..write('markedBy: $markedBy, ')
          ..write('markedAt: $markedAt, ')
          ..write('isLate: $isLate, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $AttendanceSheetsTable extends AttendanceSheets
    with TableInfo<$AttendanceSheetsTable, AttendanceSheet> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $AttendanceSheetsTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _siteIdMeta = const VerificationMeta('siteId');
  @override
  late final GeneratedColumn<String> siteId = GeneratedColumn<String>(
    'site_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES sites (id)',
    ),
  );
  static const VerificationMeta _dateMeta = const VerificationMeta('date');
  @override
  late final GeneratedColumn<String> date = GeneratedColumn<String>(
    'date',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _submittedByMeta = const VerificationMeta(
    'submittedBy',
  );
  @override
  late final GeneratedColumn<String> submittedBy = GeneratedColumn<String>(
    'submitted_by',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _submittedAtMeta = const VerificationMeta(
    'submittedAt',
  );
  @override
  late final GeneratedColumn<DateTime> submittedAt = GeneratedColumn<DateTime>(
    'submitted_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    siteId,
    date,
    submittedBy,
    submittedAt,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'attendance_sheets';
  @override
  VerificationContext validateIntegrity(
    Insertable<AttendanceSheet> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('site_id')) {
      context.handle(
        _siteIdMeta,
        siteId.isAcceptableOrUnknown(data['site_id']!, _siteIdMeta),
      );
    } else if (isInserting) {
      context.missing(_siteIdMeta);
    }
    if (data.containsKey('date')) {
      context.handle(
        _dateMeta,
        date.isAcceptableOrUnknown(data['date']!, _dateMeta),
      );
    } else if (isInserting) {
      context.missing(_dateMeta);
    }
    if (data.containsKey('submitted_by')) {
      context.handle(
        _submittedByMeta,
        submittedBy.isAcceptableOrUnknown(
          data['submitted_by']!,
          _submittedByMeta,
        ),
      );
    }
    if (data.containsKey('submitted_at')) {
      context.handle(
        _submittedAtMeta,
        submittedAt.isAcceptableOrUnknown(
          data['submitted_at']!,
          _submittedAtMeta,
        ),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  List<Set<GeneratedColumn>> get uniqueKeys => [
    {siteId, date},
  ];
  @override
  AttendanceSheet map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return AttendanceSheet(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      siteId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}site_id'],
      )!,
      date: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}date'],
      )!,
      submittedBy: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}submitted_by'],
      ),
      submittedAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}submitted_at'],
      )!,
    );
  }

  @override
  $AttendanceSheetsTable createAlias(String alias) {
    return $AttendanceSheetsTable(attachedDatabase, alias);
  }
}

class AttendanceSheet extends DataClass implements Insertable<AttendanceSheet> {
  final String id;
  final String siteId;
  final String date;
  final String? submittedBy;
  final DateTime submittedAt;
  const AttendanceSheet({
    required this.id,
    required this.siteId,
    required this.date,
    this.submittedBy,
    required this.submittedAt,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['site_id'] = Variable<String>(siteId);
    map['date'] = Variable<String>(date);
    if (!nullToAbsent || submittedBy != null) {
      map['submitted_by'] = Variable<String>(submittedBy);
    }
    map['submitted_at'] = Variable<DateTime>(submittedAt);
    return map;
  }

  AttendanceSheetsCompanion toCompanion(bool nullToAbsent) {
    return AttendanceSheetsCompanion(
      id: Value(id),
      siteId: Value(siteId),
      date: Value(date),
      submittedBy: submittedBy == null && nullToAbsent
          ? const Value.absent()
          : Value(submittedBy),
      submittedAt: Value(submittedAt),
    );
  }

  factory AttendanceSheet.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return AttendanceSheet(
      id: serializer.fromJson<String>(json['id']),
      siteId: serializer.fromJson<String>(json['siteId']),
      date: serializer.fromJson<String>(json['date']),
      submittedBy: serializer.fromJson<String?>(json['submittedBy']),
      submittedAt: serializer.fromJson<DateTime>(json['submittedAt']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'siteId': serializer.toJson<String>(siteId),
      'date': serializer.toJson<String>(date),
      'submittedBy': serializer.toJson<String?>(submittedBy),
      'submittedAt': serializer.toJson<DateTime>(submittedAt),
    };
  }

  AttendanceSheet copyWith({
    String? id,
    String? siteId,
    String? date,
    Value<String?> submittedBy = const Value.absent(),
    DateTime? submittedAt,
  }) => AttendanceSheet(
    id: id ?? this.id,
    siteId: siteId ?? this.siteId,
    date: date ?? this.date,
    submittedBy: submittedBy.present ? submittedBy.value : this.submittedBy,
    submittedAt: submittedAt ?? this.submittedAt,
  );
  AttendanceSheet copyWithCompanion(AttendanceSheetsCompanion data) {
    return AttendanceSheet(
      id: data.id.present ? data.id.value : this.id,
      siteId: data.siteId.present ? data.siteId.value : this.siteId,
      date: data.date.present ? data.date.value : this.date,
      submittedBy: data.submittedBy.present
          ? data.submittedBy.value
          : this.submittedBy,
      submittedAt: data.submittedAt.present
          ? data.submittedAt.value
          : this.submittedAt,
    );
  }

  @override
  String toString() {
    return (StringBuffer('AttendanceSheet(')
          ..write('id: $id, ')
          ..write('siteId: $siteId, ')
          ..write('date: $date, ')
          ..write('submittedBy: $submittedBy, ')
          ..write('submittedAt: $submittedAt')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(id, siteId, date, submittedBy, submittedAt);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is AttendanceSheet &&
          other.id == this.id &&
          other.siteId == this.siteId &&
          other.date == this.date &&
          other.submittedBy == this.submittedBy &&
          other.submittedAt == this.submittedAt);
}

class AttendanceSheetsCompanion extends UpdateCompanion<AttendanceSheet> {
  final Value<String> id;
  final Value<String> siteId;
  final Value<String> date;
  final Value<String?> submittedBy;
  final Value<DateTime> submittedAt;
  final Value<int> rowid;
  const AttendanceSheetsCompanion({
    this.id = const Value.absent(),
    this.siteId = const Value.absent(),
    this.date = const Value.absent(),
    this.submittedBy = const Value.absent(),
    this.submittedAt = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  AttendanceSheetsCompanion.insert({
    required String id,
    required String siteId,
    required String date,
    this.submittedBy = const Value.absent(),
    this.submittedAt = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       siteId = Value(siteId),
       date = Value(date);
  static Insertable<AttendanceSheet> custom({
    Expression<String>? id,
    Expression<String>? siteId,
    Expression<String>? date,
    Expression<String>? submittedBy,
    Expression<DateTime>? submittedAt,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (siteId != null) 'site_id': siteId,
      if (date != null) 'date': date,
      if (submittedBy != null) 'submitted_by': submittedBy,
      if (submittedAt != null) 'submitted_at': submittedAt,
      if (rowid != null) 'rowid': rowid,
    });
  }

  AttendanceSheetsCompanion copyWith({
    Value<String>? id,
    Value<String>? siteId,
    Value<String>? date,
    Value<String?>? submittedBy,
    Value<DateTime>? submittedAt,
    Value<int>? rowid,
  }) {
    return AttendanceSheetsCompanion(
      id: id ?? this.id,
      siteId: siteId ?? this.siteId,
      date: date ?? this.date,
      submittedBy: submittedBy ?? this.submittedBy,
      submittedAt: submittedAt ?? this.submittedAt,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (siteId.present) {
      map['site_id'] = Variable<String>(siteId.value);
    }
    if (date.present) {
      map['date'] = Variable<String>(date.value);
    }
    if (submittedBy.present) {
      map['submitted_by'] = Variable<String>(submittedBy.value);
    }
    if (submittedAt.present) {
      map['submitted_at'] = Variable<DateTime>(submittedAt.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('AttendanceSheetsCompanion(')
          ..write('id: $id, ')
          ..write('siteId: $siteId, ')
          ..write('date: $date, ')
          ..write('submittedBy: $submittedBy, ')
          ..write('submittedAt: $submittedAt, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $PaymentsTable extends Payments with TableInfo<$PaymentsTable, Payment> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $PaymentsTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _branchIdMeta = const VerificationMeta(
    'branchId',
  );
  @override
  late final GeneratedColumn<String> branchId = GeneratedColumn<String>(
    'branch_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES branches (id)',
    ),
  );
  static const VerificationMeta _labourIdMeta = const VerificationMeta(
    'labourId',
  );
  @override
  late final GeneratedColumn<String> labourId = GeneratedColumn<String>(
    'labour_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES labours (id)',
    ),
  );
  static const VerificationMeta _typeMeta = const VerificationMeta('type');
  @override
  late final GeneratedColumn<String> type = GeneratedColumn<String>(
    'type',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _modeMeta = const VerificationMeta('mode');
  @override
  late final GeneratedColumn<String> mode = GeneratedColumn<String>(
    'mode',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant('cash'),
  );
  static const VerificationMeta _amountMeta = const VerificationMeta('amount');
  @override
  late final GeneratedColumn<int> amount = GeneratedColumn<int>(
    'amount',
    aliasedName,
    false,
    type: DriftSqlType.int,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _dateMeta = const VerificationMeta('date');
  @override
  late final GeneratedColumn<String> date = GeneratedColumn<String>(
    'date',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _noteMeta = const VerificationMeta('note');
  @override
  late final GeneratedColumn<String> note = GeneratedColumn<String>(
    'note',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _referenceMeta = const VerificationMeta(
    'reference',
  );
  @override
  late final GeneratedColumn<String> reference = GeneratedColumn<String>(
    'reference',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _createdByMeta = const VerificationMeta(
    'createdBy',
  );
  @override
  late final GeneratedColumn<String> createdBy = GeneratedColumn<String>(
    'created_by',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _createdAtMeta = const VerificationMeta(
    'createdAt',
  );
  @override
  late final GeneratedColumn<DateTime> createdAt = GeneratedColumn<DateTime>(
    'created_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  static const VerificationMeta _voidedAtMeta = const VerificationMeta(
    'voidedAt',
  );
  @override
  late final GeneratedColumn<DateTime> voidedAt = GeneratedColumn<DateTime>(
    'voided_at',
    aliasedName,
    true,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _voidReasonMeta = const VerificationMeta(
    'voidReason',
  );
  @override
  late final GeneratedColumn<String> voidReason = GeneratedColumn<String>(
    'void_reason',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    branchId,
    labourId,
    type,
    mode,
    amount,
    date,
    note,
    reference,
    createdBy,
    createdAt,
    voidedAt,
    voidReason,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'payments';
  @override
  VerificationContext validateIntegrity(
    Insertable<Payment> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('branch_id')) {
      context.handle(
        _branchIdMeta,
        branchId.isAcceptableOrUnknown(data['branch_id']!, _branchIdMeta),
      );
    } else if (isInserting) {
      context.missing(_branchIdMeta);
    }
    if (data.containsKey('labour_id')) {
      context.handle(
        _labourIdMeta,
        labourId.isAcceptableOrUnknown(data['labour_id']!, _labourIdMeta),
      );
    } else if (isInserting) {
      context.missing(_labourIdMeta);
    }
    if (data.containsKey('type')) {
      context.handle(
        _typeMeta,
        type.isAcceptableOrUnknown(data['type']!, _typeMeta),
      );
    } else if (isInserting) {
      context.missing(_typeMeta);
    }
    if (data.containsKey('mode')) {
      context.handle(
        _modeMeta,
        mode.isAcceptableOrUnknown(data['mode']!, _modeMeta),
      );
    }
    if (data.containsKey('amount')) {
      context.handle(
        _amountMeta,
        amount.isAcceptableOrUnknown(data['amount']!, _amountMeta),
      );
    } else if (isInserting) {
      context.missing(_amountMeta);
    }
    if (data.containsKey('date')) {
      context.handle(
        _dateMeta,
        date.isAcceptableOrUnknown(data['date']!, _dateMeta),
      );
    } else if (isInserting) {
      context.missing(_dateMeta);
    }
    if (data.containsKey('note')) {
      context.handle(
        _noteMeta,
        note.isAcceptableOrUnknown(data['note']!, _noteMeta),
      );
    }
    if (data.containsKey('reference')) {
      context.handle(
        _referenceMeta,
        reference.isAcceptableOrUnknown(data['reference']!, _referenceMeta),
      );
    }
    if (data.containsKey('created_by')) {
      context.handle(
        _createdByMeta,
        createdBy.isAcceptableOrUnknown(data['created_by']!, _createdByMeta),
      );
    }
    if (data.containsKey('created_at')) {
      context.handle(
        _createdAtMeta,
        createdAt.isAcceptableOrUnknown(data['created_at']!, _createdAtMeta),
      );
    }
    if (data.containsKey('voided_at')) {
      context.handle(
        _voidedAtMeta,
        voidedAt.isAcceptableOrUnknown(data['voided_at']!, _voidedAtMeta),
      );
    }
    if (data.containsKey('void_reason')) {
      context.handle(
        _voidReasonMeta,
        voidReason.isAcceptableOrUnknown(data['void_reason']!, _voidReasonMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  Payment map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return Payment(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      branchId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}branch_id'],
      )!,
      labourId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}labour_id'],
      )!,
      type: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}type'],
      )!,
      mode: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}mode'],
      )!,
      amount: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}amount'],
      )!,
      date: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}date'],
      )!,
      note: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}note'],
      )!,
      reference: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}reference'],
      )!,
      createdBy: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}created_by'],
      ),
      createdAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}created_at'],
      )!,
      voidedAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}voided_at'],
      ),
      voidReason: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}void_reason'],
      ),
    );
  }

  @override
  $PaymentsTable createAlias(String alias) {
    return $PaymentsTable(attachedDatabase, alias);
  }
}

class Payment extends DataClass implements Insertable<Payment> {
  final String id;
  final String branchId;
  final String labourId;

  /// daily | advance | settlement | deduction
  final String type;

  /// cash | upi | bank (ignored for deductions)
  final String mode;
  final int amount;
  final String date;
  final String note;
  final String reference;
  final String? createdBy;
  final DateTime createdAt;

  /// Payments are never deleted, only voided, so history stays auditable.
  final DateTime? voidedAt;
  final String? voidReason;
  const Payment({
    required this.id,
    required this.branchId,
    required this.labourId,
    required this.type,
    required this.mode,
    required this.amount,
    required this.date,
    required this.note,
    required this.reference,
    this.createdBy,
    required this.createdAt,
    this.voidedAt,
    this.voidReason,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['branch_id'] = Variable<String>(branchId);
    map['labour_id'] = Variable<String>(labourId);
    map['type'] = Variable<String>(type);
    map['mode'] = Variable<String>(mode);
    map['amount'] = Variable<int>(amount);
    map['date'] = Variable<String>(date);
    map['note'] = Variable<String>(note);
    map['reference'] = Variable<String>(reference);
    if (!nullToAbsent || createdBy != null) {
      map['created_by'] = Variable<String>(createdBy);
    }
    map['created_at'] = Variable<DateTime>(createdAt);
    if (!nullToAbsent || voidedAt != null) {
      map['voided_at'] = Variable<DateTime>(voidedAt);
    }
    if (!nullToAbsent || voidReason != null) {
      map['void_reason'] = Variable<String>(voidReason);
    }
    return map;
  }

  PaymentsCompanion toCompanion(bool nullToAbsent) {
    return PaymentsCompanion(
      id: Value(id),
      branchId: Value(branchId),
      labourId: Value(labourId),
      type: Value(type),
      mode: Value(mode),
      amount: Value(amount),
      date: Value(date),
      note: Value(note),
      reference: Value(reference),
      createdBy: createdBy == null && nullToAbsent
          ? const Value.absent()
          : Value(createdBy),
      createdAt: Value(createdAt),
      voidedAt: voidedAt == null && nullToAbsent
          ? const Value.absent()
          : Value(voidedAt),
      voidReason: voidReason == null && nullToAbsent
          ? const Value.absent()
          : Value(voidReason),
    );
  }

  factory Payment.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return Payment(
      id: serializer.fromJson<String>(json['id']),
      branchId: serializer.fromJson<String>(json['branchId']),
      labourId: serializer.fromJson<String>(json['labourId']),
      type: serializer.fromJson<String>(json['type']),
      mode: serializer.fromJson<String>(json['mode']),
      amount: serializer.fromJson<int>(json['amount']),
      date: serializer.fromJson<String>(json['date']),
      note: serializer.fromJson<String>(json['note']),
      reference: serializer.fromJson<String>(json['reference']),
      createdBy: serializer.fromJson<String?>(json['createdBy']),
      createdAt: serializer.fromJson<DateTime>(json['createdAt']),
      voidedAt: serializer.fromJson<DateTime?>(json['voidedAt']),
      voidReason: serializer.fromJson<String?>(json['voidReason']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'branchId': serializer.toJson<String>(branchId),
      'labourId': serializer.toJson<String>(labourId),
      'type': serializer.toJson<String>(type),
      'mode': serializer.toJson<String>(mode),
      'amount': serializer.toJson<int>(amount),
      'date': serializer.toJson<String>(date),
      'note': serializer.toJson<String>(note),
      'reference': serializer.toJson<String>(reference),
      'createdBy': serializer.toJson<String?>(createdBy),
      'createdAt': serializer.toJson<DateTime>(createdAt),
      'voidedAt': serializer.toJson<DateTime?>(voidedAt),
      'voidReason': serializer.toJson<String?>(voidReason),
    };
  }

  Payment copyWith({
    String? id,
    String? branchId,
    String? labourId,
    String? type,
    String? mode,
    int? amount,
    String? date,
    String? note,
    String? reference,
    Value<String?> createdBy = const Value.absent(),
    DateTime? createdAt,
    Value<DateTime?> voidedAt = const Value.absent(),
    Value<String?> voidReason = const Value.absent(),
  }) => Payment(
    id: id ?? this.id,
    branchId: branchId ?? this.branchId,
    labourId: labourId ?? this.labourId,
    type: type ?? this.type,
    mode: mode ?? this.mode,
    amount: amount ?? this.amount,
    date: date ?? this.date,
    note: note ?? this.note,
    reference: reference ?? this.reference,
    createdBy: createdBy.present ? createdBy.value : this.createdBy,
    createdAt: createdAt ?? this.createdAt,
    voidedAt: voidedAt.present ? voidedAt.value : this.voidedAt,
    voidReason: voidReason.present ? voidReason.value : this.voidReason,
  );
  Payment copyWithCompanion(PaymentsCompanion data) {
    return Payment(
      id: data.id.present ? data.id.value : this.id,
      branchId: data.branchId.present ? data.branchId.value : this.branchId,
      labourId: data.labourId.present ? data.labourId.value : this.labourId,
      type: data.type.present ? data.type.value : this.type,
      mode: data.mode.present ? data.mode.value : this.mode,
      amount: data.amount.present ? data.amount.value : this.amount,
      date: data.date.present ? data.date.value : this.date,
      note: data.note.present ? data.note.value : this.note,
      reference: data.reference.present ? data.reference.value : this.reference,
      createdBy: data.createdBy.present ? data.createdBy.value : this.createdBy,
      createdAt: data.createdAt.present ? data.createdAt.value : this.createdAt,
      voidedAt: data.voidedAt.present ? data.voidedAt.value : this.voidedAt,
      voidReason: data.voidReason.present
          ? data.voidReason.value
          : this.voidReason,
    );
  }

  @override
  String toString() {
    return (StringBuffer('Payment(')
          ..write('id: $id, ')
          ..write('branchId: $branchId, ')
          ..write('labourId: $labourId, ')
          ..write('type: $type, ')
          ..write('mode: $mode, ')
          ..write('amount: $amount, ')
          ..write('date: $date, ')
          ..write('note: $note, ')
          ..write('reference: $reference, ')
          ..write('createdBy: $createdBy, ')
          ..write('createdAt: $createdAt, ')
          ..write('voidedAt: $voidedAt, ')
          ..write('voidReason: $voidReason')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    id,
    branchId,
    labourId,
    type,
    mode,
    amount,
    date,
    note,
    reference,
    createdBy,
    createdAt,
    voidedAt,
    voidReason,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is Payment &&
          other.id == this.id &&
          other.branchId == this.branchId &&
          other.labourId == this.labourId &&
          other.type == this.type &&
          other.mode == this.mode &&
          other.amount == this.amount &&
          other.date == this.date &&
          other.note == this.note &&
          other.reference == this.reference &&
          other.createdBy == this.createdBy &&
          other.createdAt == this.createdAt &&
          other.voidedAt == this.voidedAt &&
          other.voidReason == this.voidReason);
}

class PaymentsCompanion extends UpdateCompanion<Payment> {
  final Value<String> id;
  final Value<String> branchId;
  final Value<String> labourId;
  final Value<String> type;
  final Value<String> mode;
  final Value<int> amount;
  final Value<String> date;
  final Value<String> note;
  final Value<String> reference;
  final Value<String?> createdBy;
  final Value<DateTime> createdAt;
  final Value<DateTime?> voidedAt;
  final Value<String?> voidReason;
  final Value<int> rowid;
  const PaymentsCompanion({
    this.id = const Value.absent(),
    this.branchId = const Value.absent(),
    this.labourId = const Value.absent(),
    this.type = const Value.absent(),
    this.mode = const Value.absent(),
    this.amount = const Value.absent(),
    this.date = const Value.absent(),
    this.note = const Value.absent(),
    this.reference = const Value.absent(),
    this.createdBy = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.voidedAt = const Value.absent(),
    this.voidReason = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  PaymentsCompanion.insert({
    required String id,
    required String branchId,
    required String labourId,
    required String type,
    this.mode = const Value.absent(),
    required int amount,
    required String date,
    this.note = const Value.absent(),
    this.reference = const Value.absent(),
    this.createdBy = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.voidedAt = const Value.absent(),
    this.voidReason = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       branchId = Value(branchId),
       labourId = Value(labourId),
       type = Value(type),
       amount = Value(amount),
       date = Value(date);
  static Insertable<Payment> custom({
    Expression<String>? id,
    Expression<String>? branchId,
    Expression<String>? labourId,
    Expression<String>? type,
    Expression<String>? mode,
    Expression<int>? amount,
    Expression<String>? date,
    Expression<String>? note,
    Expression<String>? reference,
    Expression<String>? createdBy,
    Expression<DateTime>? createdAt,
    Expression<DateTime>? voidedAt,
    Expression<String>? voidReason,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (branchId != null) 'branch_id': branchId,
      if (labourId != null) 'labour_id': labourId,
      if (type != null) 'type': type,
      if (mode != null) 'mode': mode,
      if (amount != null) 'amount': amount,
      if (date != null) 'date': date,
      if (note != null) 'note': note,
      if (reference != null) 'reference': reference,
      if (createdBy != null) 'created_by': createdBy,
      if (createdAt != null) 'created_at': createdAt,
      if (voidedAt != null) 'voided_at': voidedAt,
      if (voidReason != null) 'void_reason': voidReason,
      if (rowid != null) 'rowid': rowid,
    });
  }

  PaymentsCompanion copyWith({
    Value<String>? id,
    Value<String>? branchId,
    Value<String>? labourId,
    Value<String>? type,
    Value<String>? mode,
    Value<int>? amount,
    Value<String>? date,
    Value<String>? note,
    Value<String>? reference,
    Value<String?>? createdBy,
    Value<DateTime>? createdAt,
    Value<DateTime?>? voidedAt,
    Value<String?>? voidReason,
    Value<int>? rowid,
  }) {
    return PaymentsCompanion(
      id: id ?? this.id,
      branchId: branchId ?? this.branchId,
      labourId: labourId ?? this.labourId,
      type: type ?? this.type,
      mode: mode ?? this.mode,
      amount: amount ?? this.amount,
      date: date ?? this.date,
      note: note ?? this.note,
      reference: reference ?? this.reference,
      createdBy: createdBy ?? this.createdBy,
      createdAt: createdAt ?? this.createdAt,
      voidedAt: voidedAt ?? this.voidedAt,
      voidReason: voidReason ?? this.voidReason,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (branchId.present) {
      map['branch_id'] = Variable<String>(branchId.value);
    }
    if (labourId.present) {
      map['labour_id'] = Variable<String>(labourId.value);
    }
    if (type.present) {
      map['type'] = Variable<String>(type.value);
    }
    if (mode.present) {
      map['mode'] = Variable<String>(mode.value);
    }
    if (amount.present) {
      map['amount'] = Variable<int>(amount.value);
    }
    if (date.present) {
      map['date'] = Variable<String>(date.value);
    }
    if (note.present) {
      map['note'] = Variable<String>(note.value);
    }
    if (reference.present) {
      map['reference'] = Variable<String>(reference.value);
    }
    if (createdBy.present) {
      map['created_by'] = Variable<String>(createdBy.value);
    }
    if (createdAt.present) {
      map['created_at'] = Variable<DateTime>(createdAt.value);
    }
    if (voidedAt.present) {
      map['voided_at'] = Variable<DateTime>(voidedAt.value);
    }
    if (voidReason.present) {
      map['void_reason'] = Variable<String>(voidReason.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('PaymentsCompanion(')
          ..write('id: $id, ')
          ..write('branchId: $branchId, ')
          ..write('labourId: $labourId, ')
          ..write('type: $type, ')
          ..write('mode: $mode, ')
          ..write('amount: $amount, ')
          ..write('date: $date, ')
          ..write('note: $note, ')
          ..write('reference: $reference, ')
          ..write('createdBy: $createdBy, ')
          ..write('createdAt: $createdAt, ')
          ..write('voidedAt: $voidedAt, ')
          ..write('voidReason: $voidReason, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $InvoicesTable extends Invoices with TableInfo<$InvoicesTable, Invoice> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $InvoicesTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _branchIdMeta = const VerificationMeta(
    'branchId',
  );
  @override
  late final GeneratedColumn<String> branchId = GeneratedColumn<String>(
    'branch_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES branches (id)',
    ),
  );
  static const VerificationMeta _companyIdMeta = const VerificationMeta(
    'companyId',
  );
  @override
  late final GeneratedColumn<String> companyId = GeneratedColumn<String>(
    'company_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES companies (id)',
    ),
  );
  static const VerificationMeta _siteIdMeta = const VerificationMeta('siteId');
  @override
  late final GeneratedColumn<String> siteId = GeneratedColumn<String>(
    'site_id',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES sites (id)',
    ),
  );
  static const VerificationMeta _numberMeta = const VerificationMeta('number');
  @override
  late final GeneratedColumn<String> number = GeneratedColumn<String>(
    'number',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _issueDateMeta = const VerificationMeta(
    'issueDate',
  );
  @override
  late final GeneratedColumn<String> issueDate = GeneratedColumn<String>(
    'issue_date',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _dueDateMeta = const VerificationMeta(
    'dueDate',
  );
  @override
  late final GeneratedColumn<String> dueDate = GeneratedColumn<String>(
    'due_date',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _periodFromMeta = const VerificationMeta(
    'periodFrom',
  );
  @override
  late final GeneratedColumn<String> periodFrom = GeneratedColumn<String>(
    'period_from',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _periodToMeta = const VerificationMeta(
    'periodTo',
  );
  @override
  late final GeneratedColumn<String> periodTo = GeneratedColumn<String>(
    'period_to',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _subtotalMeta = const VerificationMeta(
    'subtotal',
  );
  @override
  late final GeneratedColumn<int> subtotal = GeneratedColumn<int>(
    'subtotal',
    aliasedName,
    false,
    type: DriftSqlType.int,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _taxRateMeta = const VerificationMeta(
    'taxRate',
  );
  @override
  late final GeneratedColumn<double> taxRate = GeneratedColumn<double>(
    'tax_rate',
    aliasedName,
    false,
    type: DriftSqlType.double,
    requiredDuringInsert: false,
    defaultValue: const Constant(0),
  );
  static const VerificationMeta _taxLabelMeta = const VerificationMeta(
    'taxLabel',
  );
  @override
  late final GeneratedColumn<String> taxLabel = GeneratedColumn<String>(
    'tax_label',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant('GST'),
  );
  static const VerificationMeta _taxAmountMeta = const VerificationMeta(
    'taxAmount',
  );
  @override
  late final GeneratedColumn<int> taxAmount = GeneratedColumn<int>(
    'tax_amount',
    aliasedName,
    false,
    type: DriftSqlType.int,
    requiredDuringInsert: false,
    defaultValue: const Constant(0),
  );
  static const VerificationMeta _totalMeta = const VerificationMeta('total');
  @override
  late final GeneratedColumn<int> total = GeneratedColumn<int>(
    'total',
    aliasedName,
    false,
    type: DriftSqlType.int,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _statusMeta = const VerificationMeta('status');
  @override
  late final GeneratedColumn<String> status = GeneratedColumn<String>(
    'status',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant('issued'),
  );
  static const VerificationMeta _notesMeta = const VerificationMeta('notes');
  @override
  late final GeneratedColumn<String> notes = GeneratedColumn<String>(
    'notes',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _createdAtMeta = const VerificationMeta(
    'createdAt',
  );
  @override
  late final GeneratedColumn<DateTime> createdAt = GeneratedColumn<DateTime>(
    'created_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    branchId,
    companyId,
    siteId,
    number,
    issueDate,
    dueDate,
    periodFrom,
    periodTo,
    subtotal,
    taxRate,
    taxLabel,
    taxAmount,
    total,
    status,
    notes,
    createdAt,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'invoices';
  @override
  VerificationContext validateIntegrity(
    Insertable<Invoice> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('branch_id')) {
      context.handle(
        _branchIdMeta,
        branchId.isAcceptableOrUnknown(data['branch_id']!, _branchIdMeta),
      );
    } else if (isInserting) {
      context.missing(_branchIdMeta);
    }
    if (data.containsKey('company_id')) {
      context.handle(
        _companyIdMeta,
        companyId.isAcceptableOrUnknown(data['company_id']!, _companyIdMeta),
      );
    } else if (isInserting) {
      context.missing(_companyIdMeta);
    }
    if (data.containsKey('site_id')) {
      context.handle(
        _siteIdMeta,
        siteId.isAcceptableOrUnknown(data['site_id']!, _siteIdMeta),
      );
    }
    if (data.containsKey('number')) {
      context.handle(
        _numberMeta,
        number.isAcceptableOrUnknown(data['number']!, _numberMeta),
      );
    } else if (isInserting) {
      context.missing(_numberMeta);
    }
    if (data.containsKey('issue_date')) {
      context.handle(
        _issueDateMeta,
        issueDate.isAcceptableOrUnknown(data['issue_date']!, _issueDateMeta),
      );
    } else if (isInserting) {
      context.missing(_issueDateMeta);
    }
    if (data.containsKey('due_date')) {
      context.handle(
        _dueDateMeta,
        dueDate.isAcceptableOrUnknown(data['due_date']!, _dueDateMeta),
      );
    } else if (isInserting) {
      context.missing(_dueDateMeta);
    }
    if (data.containsKey('period_from')) {
      context.handle(
        _periodFromMeta,
        periodFrom.isAcceptableOrUnknown(data['period_from']!, _periodFromMeta),
      );
    } else if (isInserting) {
      context.missing(_periodFromMeta);
    }
    if (data.containsKey('period_to')) {
      context.handle(
        _periodToMeta,
        periodTo.isAcceptableOrUnknown(data['period_to']!, _periodToMeta),
      );
    } else if (isInserting) {
      context.missing(_periodToMeta);
    }
    if (data.containsKey('subtotal')) {
      context.handle(
        _subtotalMeta,
        subtotal.isAcceptableOrUnknown(data['subtotal']!, _subtotalMeta),
      );
    } else if (isInserting) {
      context.missing(_subtotalMeta);
    }
    if (data.containsKey('tax_rate')) {
      context.handle(
        _taxRateMeta,
        taxRate.isAcceptableOrUnknown(data['tax_rate']!, _taxRateMeta),
      );
    }
    if (data.containsKey('tax_label')) {
      context.handle(
        _taxLabelMeta,
        taxLabel.isAcceptableOrUnknown(data['tax_label']!, _taxLabelMeta),
      );
    }
    if (data.containsKey('tax_amount')) {
      context.handle(
        _taxAmountMeta,
        taxAmount.isAcceptableOrUnknown(data['tax_amount']!, _taxAmountMeta),
      );
    }
    if (data.containsKey('total')) {
      context.handle(
        _totalMeta,
        total.isAcceptableOrUnknown(data['total']!, _totalMeta),
      );
    } else if (isInserting) {
      context.missing(_totalMeta);
    }
    if (data.containsKey('status')) {
      context.handle(
        _statusMeta,
        status.isAcceptableOrUnknown(data['status']!, _statusMeta),
      );
    }
    if (data.containsKey('notes')) {
      context.handle(
        _notesMeta,
        notes.isAcceptableOrUnknown(data['notes']!, _notesMeta),
      );
    }
    if (data.containsKey('created_at')) {
      context.handle(
        _createdAtMeta,
        createdAt.isAcceptableOrUnknown(data['created_at']!, _createdAtMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  Invoice map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return Invoice(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      branchId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}branch_id'],
      )!,
      companyId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}company_id'],
      )!,
      siteId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}site_id'],
      ),
      number: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}number'],
      )!,
      issueDate: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}issue_date'],
      )!,
      dueDate: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}due_date'],
      )!,
      periodFrom: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}period_from'],
      )!,
      periodTo: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}period_to'],
      )!,
      subtotal: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}subtotal'],
      )!,
      taxRate: attachedDatabase.typeMapping.read(
        DriftSqlType.double,
        data['${effectivePrefix}tax_rate'],
      )!,
      taxLabel: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}tax_label'],
      )!,
      taxAmount: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}tax_amount'],
      )!,
      total: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}total'],
      )!,
      status: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}status'],
      )!,
      notes: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}notes'],
      )!,
      createdAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}created_at'],
      )!,
    );
  }

  @override
  $InvoicesTable createAlias(String alias) {
    return $InvoicesTable(attachedDatabase, alias);
  }
}

class Invoice extends DataClass implements Insertable<Invoice> {
  final String id;
  final String branchId;
  final String companyId;
  final String? siteId;
  final String number;
  final String issueDate;
  final String dueDate;
  final String periodFrom;
  final String periodTo;
  final int subtotal;
  final double taxRate;
  final String taxLabel;
  final int taxAmount;
  final int total;

  /// issued | cancelled
  final String status;
  final String notes;
  final DateTime createdAt;
  const Invoice({
    required this.id,
    required this.branchId,
    required this.companyId,
    this.siteId,
    required this.number,
    required this.issueDate,
    required this.dueDate,
    required this.periodFrom,
    required this.periodTo,
    required this.subtotal,
    required this.taxRate,
    required this.taxLabel,
    required this.taxAmount,
    required this.total,
    required this.status,
    required this.notes,
    required this.createdAt,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['branch_id'] = Variable<String>(branchId);
    map['company_id'] = Variable<String>(companyId);
    if (!nullToAbsent || siteId != null) {
      map['site_id'] = Variable<String>(siteId);
    }
    map['number'] = Variable<String>(number);
    map['issue_date'] = Variable<String>(issueDate);
    map['due_date'] = Variable<String>(dueDate);
    map['period_from'] = Variable<String>(periodFrom);
    map['period_to'] = Variable<String>(periodTo);
    map['subtotal'] = Variable<int>(subtotal);
    map['tax_rate'] = Variable<double>(taxRate);
    map['tax_label'] = Variable<String>(taxLabel);
    map['tax_amount'] = Variable<int>(taxAmount);
    map['total'] = Variable<int>(total);
    map['status'] = Variable<String>(status);
    map['notes'] = Variable<String>(notes);
    map['created_at'] = Variable<DateTime>(createdAt);
    return map;
  }

  InvoicesCompanion toCompanion(bool nullToAbsent) {
    return InvoicesCompanion(
      id: Value(id),
      branchId: Value(branchId),
      companyId: Value(companyId),
      siteId: siteId == null && nullToAbsent
          ? const Value.absent()
          : Value(siteId),
      number: Value(number),
      issueDate: Value(issueDate),
      dueDate: Value(dueDate),
      periodFrom: Value(periodFrom),
      periodTo: Value(periodTo),
      subtotal: Value(subtotal),
      taxRate: Value(taxRate),
      taxLabel: Value(taxLabel),
      taxAmount: Value(taxAmount),
      total: Value(total),
      status: Value(status),
      notes: Value(notes),
      createdAt: Value(createdAt),
    );
  }

  factory Invoice.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return Invoice(
      id: serializer.fromJson<String>(json['id']),
      branchId: serializer.fromJson<String>(json['branchId']),
      companyId: serializer.fromJson<String>(json['companyId']),
      siteId: serializer.fromJson<String?>(json['siteId']),
      number: serializer.fromJson<String>(json['number']),
      issueDate: serializer.fromJson<String>(json['issueDate']),
      dueDate: serializer.fromJson<String>(json['dueDate']),
      periodFrom: serializer.fromJson<String>(json['periodFrom']),
      periodTo: serializer.fromJson<String>(json['periodTo']),
      subtotal: serializer.fromJson<int>(json['subtotal']),
      taxRate: serializer.fromJson<double>(json['taxRate']),
      taxLabel: serializer.fromJson<String>(json['taxLabel']),
      taxAmount: serializer.fromJson<int>(json['taxAmount']),
      total: serializer.fromJson<int>(json['total']),
      status: serializer.fromJson<String>(json['status']),
      notes: serializer.fromJson<String>(json['notes']),
      createdAt: serializer.fromJson<DateTime>(json['createdAt']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'branchId': serializer.toJson<String>(branchId),
      'companyId': serializer.toJson<String>(companyId),
      'siteId': serializer.toJson<String?>(siteId),
      'number': serializer.toJson<String>(number),
      'issueDate': serializer.toJson<String>(issueDate),
      'dueDate': serializer.toJson<String>(dueDate),
      'periodFrom': serializer.toJson<String>(periodFrom),
      'periodTo': serializer.toJson<String>(periodTo),
      'subtotal': serializer.toJson<int>(subtotal),
      'taxRate': serializer.toJson<double>(taxRate),
      'taxLabel': serializer.toJson<String>(taxLabel),
      'taxAmount': serializer.toJson<int>(taxAmount),
      'total': serializer.toJson<int>(total),
      'status': serializer.toJson<String>(status),
      'notes': serializer.toJson<String>(notes),
      'createdAt': serializer.toJson<DateTime>(createdAt),
    };
  }

  Invoice copyWith({
    String? id,
    String? branchId,
    String? companyId,
    Value<String?> siteId = const Value.absent(),
    String? number,
    String? issueDate,
    String? dueDate,
    String? periodFrom,
    String? periodTo,
    int? subtotal,
    double? taxRate,
    String? taxLabel,
    int? taxAmount,
    int? total,
    String? status,
    String? notes,
    DateTime? createdAt,
  }) => Invoice(
    id: id ?? this.id,
    branchId: branchId ?? this.branchId,
    companyId: companyId ?? this.companyId,
    siteId: siteId.present ? siteId.value : this.siteId,
    number: number ?? this.number,
    issueDate: issueDate ?? this.issueDate,
    dueDate: dueDate ?? this.dueDate,
    periodFrom: periodFrom ?? this.periodFrom,
    periodTo: periodTo ?? this.periodTo,
    subtotal: subtotal ?? this.subtotal,
    taxRate: taxRate ?? this.taxRate,
    taxLabel: taxLabel ?? this.taxLabel,
    taxAmount: taxAmount ?? this.taxAmount,
    total: total ?? this.total,
    status: status ?? this.status,
    notes: notes ?? this.notes,
    createdAt: createdAt ?? this.createdAt,
  );
  Invoice copyWithCompanion(InvoicesCompanion data) {
    return Invoice(
      id: data.id.present ? data.id.value : this.id,
      branchId: data.branchId.present ? data.branchId.value : this.branchId,
      companyId: data.companyId.present ? data.companyId.value : this.companyId,
      siteId: data.siteId.present ? data.siteId.value : this.siteId,
      number: data.number.present ? data.number.value : this.number,
      issueDate: data.issueDate.present ? data.issueDate.value : this.issueDate,
      dueDate: data.dueDate.present ? data.dueDate.value : this.dueDate,
      periodFrom: data.periodFrom.present
          ? data.periodFrom.value
          : this.periodFrom,
      periodTo: data.periodTo.present ? data.periodTo.value : this.periodTo,
      subtotal: data.subtotal.present ? data.subtotal.value : this.subtotal,
      taxRate: data.taxRate.present ? data.taxRate.value : this.taxRate,
      taxLabel: data.taxLabel.present ? data.taxLabel.value : this.taxLabel,
      taxAmount: data.taxAmount.present ? data.taxAmount.value : this.taxAmount,
      total: data.total.present ? data.total.value : this.total,
      status: data.status.present ? data.status.value : this.status,
      notes: data.notes.present ? data.notes.value : this.notes,
      createdAt: data.createdAt.present ? data.createdAt.value : this.createdAt,
    );
  }

  @override
  String toString() {
    return (StringBuffer('Invoice(')
          ..write('id: $id, ')
          ..write('branchId: $branchId, ')
          ..write('companyId: $companyId, ')
          ..write('siteId: $siteId, ')
          ..write('number: $number, ')
          ..write('issueDate: $issueDate, ')
          ..write('dueDate: $dueDate, ')
          ..write('periodFrom: $periodFrom, ')
          ..write('periodTo: $periodTo, ')
          ..write('subtotal: $subtotal, ')
          ..write('taxRate: $taxRate, ')
          ..write('taxLabel: $taxLabel, ')
          ..write('taxAmount: $taxAmount, ')
          ..write('total: $total, ')
          ..write('status: $status, ')
          ..write('notes: $notes, ')
          ..write('createdAt: $createdAt')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    id,
    branchId,
    companyId,
    siteId,
    number,
    issueDate,
    dueDate,
    periodFrom,
    periodTo,
    subtotal,
    taxRate,
    taxLabel,
    taxAmount,
    total,
    status,
    notes,
    createdAt,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is Invoice &&
          other.id == this.id &&
          other.branchId == this.branchId &&
          other.companyId == this.companyId &&
          other.siteId == this.siteId &&
          other.number == this.number &&
          other.issueDate == this.issueDate &&
          other.dueDate == this.dueDate &&
          other.periodFrom == this.periodFrom &&
          other.periodTo == this.periodTo &&
          other.subtotal == this.subtotal &&
          other.taxRate == this.taxRate &&
          other.taxLabel == this.taxLabel &&
          other.taxAmount == this.taxAmount &&
          other.total == this.total &&
          other.status == this.status &&
          other.notes == this.notes &&
          other.createdAt == this.createdAt);
}

class InvoicesCompanion extends UpdateCompanion<Invoice> {
  final Value<String> id;
  final Value<String> branchId;
  final Value<String> companyId;
  final Value<String?> siteId;
  final Value<String> number;
  final Value<String> issueDate;
  final Value<String> dueDate;
  final Value<String> periodFrom;
  final Value<String> periodTo;
  final Value<int> subtotal;
  final Value<double> taxRate;
  final Value<String> taxLabel;
  final Value<int> taxAmount;
  final Value<int> total;
  final Value<String> status;
  final Value<String> notes;
  final Value<DateTime> createdAt;
  final Value<int> rowid;
  const InvoicesCompanion({
    this.id = const Value.absent(),
    this.branchId = const Value.absent(),
    this.companyId = const Value.absent(),
    this.siteId = const Value.absent(),
    this.number = const Value.absent(),
    this.issueDate = const Value.absent(),
    this.dueDate = const Value.absent(),
    this.periodFrom = const Value.absent(),
    this.periodTo = const Value.absent(),
    this.subtotal = const Value.absent(),
    this.taxRate = const Value.absent(),
    this.taxLabel = const Value.absent(),
    this.taxAmount = const Value.absent(),
    this.total = const Value.absent(),
    this.status = const Value.absent(),
    this.notes = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  InvoicesCompanion.insert({
    required String id,
    required String branchId,
    required String companyId,
    this.siteId = const Value.absent(),
    required String number,
    required String issueDate,
    required String dueDate,
    required String periodFrom,
    required String periodTo,
    required int subtotal,
    this.taxRate = const Value.absent(),
    this.taxLabel = const Value.absent(),
    this.taxAmount = const Value.absent(),
    required int total,
    this.status = const Value.absent(),
    this.notes = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       branchId = Value(branchId),
       companyId = Value(companyId),
       number = Value(number),
       issueDate = Value(issueDate),
       dueDate = Value(dueDate),
       periodFrom = Value(periodFrom),
       periodTo = Value(periodTo),
       subtotal = Value(subtotal),
       total = Value(total);
  static Insertable<Invoice> custom({
    Expression<String>? id,
    Expression<String>? branchId,
    Expression<String>? companyId,
    Expression<String>? siteId,
    Expression<String>? number,
    Expression<String>? issueDate,
    Expression<String>? dueDate,
    Expression<String>? periodFrom,
    Expression<String>? periodTo,
    Expression<int>? subtotal,
    Expression<double>? taxRate,
    Expression<String>? taxLabel,
    Expression<int>? taxAmount,
    Expression<int>? total,
    Expression<String>? status,
    Expression<String>? notes,
    Expression<DateTime>? createdAt,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (branchId != null) 'branch_id': branchId,
      if (companyId != null) 'company_id': companyId,
      if (siteId != null) 'site_id': siteId,
      if (number != null) 'number': number,
      if (issueDate != null) 'issue_date': issueDate,
      if (dueDate != null) 'due_date': dueDate,
      if (periodFrom != null) 'period_from': periodFrom,
      if (periodTo != null) 'period_to': periodTo,
      if (subtotal != null) 'subtotal': subtotal,
      if (taxRate != null) 'tax_rate': taxRate,
      if (taxLabel != null) 'tax_label': taxLabel,
      if (taxAmount != null) 'tax_amount': taxAmount,
      if (total != null) 'total': total,
      if (status != null) 'status': status,
      if (notes != null) 'notes': notes,
      if (createdAt != null) 'created_at': createdAt,
      if (rowid != null) 'rowid': rowid,
    });
  }

  InvoicesCompanion copyWith({
    Value<String>? id,
    Value<String>? branchId,
    Value<String>? companyId,
    Value<String?>? siteId,
    Value<String>? number,
    Value<String>? issueDate,
    Value<String>? dueDate,
    Value<String>? periodFrom,
    Value<String>? periodTo,
    Value<int>? subtotal,
    Value<double>? taxRate,
    Value<String>? taxLabel,
    Value<int>? taxAmount,
    Value<int>? total,
    Value<String>? status,
    Value<String>? notes,
    Value<DateTime>? createdAt,
    Value<int>? rowid,
  }) {
    return InvoicesCompanion(
      id: id ?? this.id,
      branchId: branchId ?? this.branchId,
      companyId: companyId ?? this.companyId,
      siteId: siteId ?? this.siteId,
      number: number ?? this.number,
      issueDate: issueDate ?? this.issueDate,
      dueDate: dueDate ?? this.dueDate,
      periodFrom: periodFrom ?? this.periodFrom,
      periodTo: periodTo ?? this.periodTo,
      subtotal: subtotal ?? this.subtotal,
      taxRate: taxRate ?? this.taxRate,
      taxLabel: taxLabel ?? this.taxLabel,
      taxAmount: taxAmount ?? this.taxAmount,
      total: total ?? this.total,
      status: status ?? this.status,
      notes: notes ?? this.notes,
      createdAt: createdAt ?? this.createdAt,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (branchId.present) {
      map['branch_id'] = Variable<String>(branchId.value);
    }
    if (companyId.present) {
      map['company_id'] = Variable<String>(companyId.value);
    }
    if (siteId.present) {
      map['site_id'] = Variable<String>(siteId.value);
    }
    if (number.present) {
      map['number'] = Variable<String>(number.value);
    }
    if (issueDate.present) {
      map['issue_date'] = Variable<String>(issueDate.value);
    }
    if (dueDate.present) {
      map['due_date'] = Variable<String>(dueDate.value);
    }
    if (periodFrom.present) {
      map['period_from'] = Variable<String>(periodFrom.value);
    }
    if (periodTo.present) {
      map['period_to'] = Variable<String>(periodTo.value);
    }
    if (subtotal.present) {
      map['subtotal'] = Variable<int>(subtotal.value);
    }
    if (taxRate.present) {
      map['tax_rate'] = Variable<double>(taxRate.value);
    }
    if (taxLabel.present) {
      map['tax_label'] = Variable<String>(taxLabel.value);
    }
    if (taxAmount.present) {
      map['tax_amount'] = Variable<int>(taxAmount.value);
    }
    if (total.present) {
      map['total'] = Variable<int>(total.value);
    }
    if (status.present) {
      map['status'] = Variable<String>(status.value);
    }
    if (notes.present) {
      map['notes'] = Variable<String>(notes.value);
    }
    if (createdAt.present) {
      map['created_at'] = Variable<DateTime>(createdAt.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('InvoicesCompanion(')
          ..write('id: $id, ')
          ..write('branchId: $branchId, ')
          ..write('companyId: $companyId, ')
          ..write('siteId: $siteId, ')
          ..write('number: $number, ')
          ..write('issueDate: $issueDate, ')
          ..write('dueDate: $dueDate, ')
          ..write('periodFrom: $periodFrom, ')
          ..write('periodTo: $periodTo, ')
          ..write('subtotal: $subtotal, ')
          ..write('taxRate: $taxRate, ')
          ..write('taxLabel: $taxLabel, ')
          ..write('taxAmount: $taxAmount, ')
          ..write('total: $total, ')
          ..write('status: $status, ')
          ..write('notes: $notes, ')
          ..write('createdAt: $createdAt, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $InvoiceLinesTable extends InvoiceLines
    with TableInfo<$InvoiceLinesTable, InvoiceLine> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $InvoiceLinesTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _invoiceIdMeta = const VerificationMeta(
    'invoiceId',
  );
  @override
  late final GeneratedColumn<String> invoiceId = GeneratedColumn<String>(
    'invoice_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES invoices (id)',
    ),
  );
  static const VerificationMeta _labourIdMeta = const VerificationMeta(
    'labourId',
  );
  @override
  late final GeneratedColumn<String> labourId = GeneratedColumn<String>(
    'labour_id',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES labours (id)',
    ),
  );
  static const VerificationMeta _descriptionMeta = const VerificationMeta(
    'description',
  );
  @override
  late final GeneratedColumn<String> description = GeneratedColumn<String>(
    'description',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _skillMeta = const VerificationMeta('skill');
  @override
  late final GeneratedColumn<String> skill = GeneratedColumn<String>(
    'skill',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _daysMeta = const VerificationMeta('days');
  @override
  late final GeneratedColumn<double> days = GeneratedColumn<double>(
    'days',
    aliasedName,
    false,
    type: DriftSqlType.double,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _rateMeta = const VerificationMeta('rate');
  @override
  late final GeneratedColumn<int> rate = GeneratedColumn<int>(
    'rate',
    aliasedName,
    false,
    type: DriftSqlType.int,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _otHoursMeta = const VerificationMeta(
    'otHours',
  );
  @override
  late final GeneratedColumn<double> otHours = GeneratedColumn<double>(
    'ot_hours',
    aliasedName,
    false,
    type: DriftSqlType.double,
    requiredDuringInsert: false,
    defaultValue: const Constant(0),
  );
  static const VerificationMeta _otRateMeta = const VerificationMeta('otRate');
  @override
  late final GeneratedColumn<int> otRate = GeneratedColumn<int>(
    'ot_rate',
    aliasedName,
    false,
    type: DriftSqlType.int,
    requiredDuringInsert: false,
    defaultValue: const Constant(0),
  );
  static const VerificationMeta _amountMeta = const VerificationMeta('amount');
  @override
  late final GeneratedColumn<int> amount = GeneratedColumn<int>(
    'amount',
    aliasedName,
    false,
    type: DriftSqlType.int,
    requiredDuringInsert: true,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    invoiceId,
    labourId,
    description,
    skill,
    days,
    rate,
    otHours,
    otRate,
    amount,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'invoice_lines';
  @override
  VerificationContext validateIntegrity(
    Insertable<InvoiceLine> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('invoice_id')) {
      context.handle(
        _invoiceIdMeta,
        invoiceId.isAcceptableOrUnknown(data['invoice_id']!, _invoiceIdMeta),
      );
    } else if (isInserting) {
      context.missing(_invoiceIdMeta);
    }
    if (data.containsKey('labour_id')) {
      context.handle(
        _labourIdMeta,
        labourId.isAcceptableOrUnknown(data['labour_id']!, _labourIdMeta),
      );
    }
    if (data.containsKey('description')) {
      context.handle(
        _descriptionMeta,
        description.isAcceptableOrUnknown(
          data['description']!,
          _descriptionMeta,
        ),
      );
    } else if (isInserting) {
      context.missing(_descriptionMeta);
    }
    if (data.containsKey('skill')) {
      context.handle(
        _skillMeta,
        skill.isAcceptableOrUnknown(data['skill']!, _skillMeta),
      );
    }
    if (data.containsKey('days')) {
      context.handle(
        _daysMeta,
        days.isAcceptableOrUnknown(data['days']!, _daysMeta),
      );
    } else if (isInserting) {
      context.missing(_daysMeta);
    }
    if (data.containsKey('rate')) {
      context.handle(
        _rateMeta,
        rate.isAcceptableOrUnknown(data['rate']!, _rateMeta),
      );
    } else if (isInserting) {
      context.missing(_rateMeta);
    }
    if (data.containsKey('ot_hours')) {
      context.handle(
        _otHoursMeta,
        otHours.isAcceptableOrUnknown(data['ot_hours']!, _otHoursMeta),
      );
    }
    if (data.containsKey('ot_rate')) {
      context.handle(
        _otRateMeta,
        otRate.isAcceptableOrUnknown(data['ot_rate']!, _otRateMeta),
      );
    }
    if (data.containsKey('amount')) {
      context.handle(
        _amountMeta,
        amount.isAcceptableOrUnknown(data['amount']!, _amountMeta),
      );
    } else if (isInserting) {
      context.missing(_amountMeta);
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  InvoiceLine map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return InvoiceLine(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      invoiceId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}invoice_id'],
      )!,
      labourId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}labour_id'],
      ),
      description: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}description'],
      )!,
      skill: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}skill'],
      )!,
      days: attachedDatabase.typeMapping.read(
        DriftSqlType.double,
        data['${effectivePrefix}days'],
      )!,
      rate: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}rate'],
      )!,
      otHours: attachedDatabase.typeMapping.read(
        DriftSqlType.double,
        data['${effectivePrefix}ot_hours'],
      )!,
      otRate: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}ot_rate'],
      )!,
      amount: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}amount'],
      )!,
    );
  }

  @override
  $InvoiceLinesTable createAlias(String alias) {
    return $InvoiceLinesTable(attachedDatabase, alias);
  }
}

class InvoiceLine extends DataClass implements Insertable<InvoiceLine> {
  final String id;
  final String invoiceId;
  final String? labourId;
  final String description;
  final String skill;
  final double days;
  final int rate;
  final double otHours;
  final int otRate;
  final int amount;
  const InvoiceLine({
    required this.id,
    required this.invoiceId,
    this.labourId,
    required this.description,
    required this.skill,
    required this.days,
    required this.rate,
    required this.otHours,
    required this.otRate,
    required this.amount,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['invoice_id'] = Variable<String>(invoiceId);
    if (!nullToAbsent || labourId != null) {
      map['labour_id'] = Variable<String>(labourId);
    }
    map['description'] = Variable<String>(description);
    map['skill'] = Variable<String>(skill);
    map['days'] = Variable<double>(days);
    map['rate'] = Variable<int>(rate);
    map['ot_hours'] = Variable<double>(otHours);
    map['ot_rate'] = Variable<int>(otRate);
    map['amount'] = Variable<int>(amount);
    return map;
  }

  InvoiceLinesCompanion toCompanion(bool nullToAbsent) {
    return InvoiceLinesCompanion(
      id: Value(id),
      invoiceId: Value(invoiceId),
      labourId: labourId == null && nullToAbsent
          ? const Value.absent()
          : Value(labourId),
      description: Value(description),
      skill: Value(skill),
      days: Value(days),
      rate: Value(rate),
      otHours: Value(otHours),
      otRate: Value(otRate),
      amount: Value(amount),
    );
  }

  factory InvoiceLine.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return InvoiceLine(
      id: serializer.fromJson<String>(json['id']),
      invoiceId: serializer.fromJson<String>(json['invoiceId']),
      labourId: serializer.fromJson<String?>(json['labourId']),
      description: serializer.fromJson<String>(json['description']),
      skill: serializer.fromJson<String>(json['skill']),
      days: serializer.fromJson<double>(json['days']),
      rate: serializer.fromJson<int>(json['rate']),
      otHours: serializer.fromJson<double>(json['otHours']),
      otRate: serializer.fromJson<int>(json['otRate']),
      amount: serializer.fromJson<int>(json['amount']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'invoiceId': serializer.toJson<String>(invoiceId),
      'labourId': serializer.toJson<String?>(labourId),
      'description': serializer.toJson<String>(description),
      'skill': serializer.toJson<String>(skill),
      'days': serializer.toJson<double>(days),
      'rate': serializer.toJson<int>(rate),
      'otHours': serializer.toJson<double>(otHours),
      'otRate': serializer.toJson<int>(otRate),
      'amount': serializer.toJson<int>(amount),
    };
  }

  InvoiceLine copyWith({
    String? id,
    String? invoiceId,
    Value<String?> labourId = const Value.absent(),
    String? description,
    String? skill,
    double? days,
    int? rate,
    double? otHours,
    int? otRate,
    int? amount,
  }) => InvoiceLine(
    id: id ?? this.id,
    invoiceId: invoiceId ?? this.invoiceId,
    labourId: labourId.present ? labourId.value : this.labourId,
    description: description ?? this.description,
    skill: skill ?? this.skill,
    days: days ?? this.days,
    rate: rate ?? this.rate,
    otHours: otHours ?? this.otHours,
    otRate: otRate ?? this.otRate,
    amount: amount ?? this.amount,
  );
  InvoiceLine copyWithCompanion(InvoiceLinesCompanion data) {
    return InvoiceLine(
      id: data.id.present ? data.id.value : this.id,
      invoiceId: data.invoiceId.present ? data.invoiceId.value : this.invoiceId,
      labourId: data.labourId.present ? data.labourId.value : this.labourId,
      description: data.description.present
          ? data.description.value
          : this.description,
      skill: data.skill.present ? data.skill.value : this.skill,
      days: data.days.present ? data.days.value : this.days,
      rate: data.rate.present ? data.rate.value : this.rate,
      otHours: data.otHours.present ? data.otHours.value : this.otHours,
      otRate: data.otRate.present ? data.otRate.value : this.otRate,
      amount: data.amount.present ? data.amount.value : this.amount,
    );
  }

  @override
  String toString() {
    return (StringBuffer('InvoiceLine(')
          ..write('id: $id, ')
          ..write('invoiceId: $invoiceId, ')
          ..write('labourId: $labourId, ')
          ..write('description: $description, ')
          ..write('skill: $skill, ')
          ..write('days: $days, ')
          ..write('rate: $rate, ')
          ..write('otHours: $otHours, ')
          ..write('otRate: $otRate, ')
          ..write('amount: $amount')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    id,
    invoiceId,
    labourId,
    description,
    skill,
    days,
    rate,
    otHours,
    otRate,
    amount,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is InvoiceLine &&
          other.id == this.id &&
          other.invoiceId == this.invoiceId &&
          other.labourId == this.labourId &&
          other.description == this.description &&
          other.skill == this.skill &&
          other.days == this.days &&
          other.rate == this.rate &&
          other.otHours == this.otHours &&
          other.otRate == this.otRate &&
          other.amount == this.amount);
}

class InvoiceLinesCompanion extends UpdateCompanion<InvoiceLine> {
  final Value<String> id;
  final Value<String> invoiceId;
  final Value<String?> labourId;
  final Value<String> description;
  final Value<String> skill;
  final Value<double> days;
  final Value<int> rate;
  final Value<double> otHours;
  final Value<int> otRate;
  final Value<int> amount;
  final Value<int> rowid;
  const InvoiceLinesCompanion({
    this.id = const Value.absent(),
    this.invoiceId = const Value.absent(),
    this.labourId = const Value.absent(),
    this.description = const Value.absent(),
    this.skill = const Value.absent(),
    this.days = const Value.absent(),
    this.rate = const Value.absent(),
    this.otHours = const Value.absent(),
    this.otRate = const Value.absent(),
    this.amount = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  InvoiceLinesCompanion.insert({
    required String id,
    required String invoiceId,
    this.labourId = const Value.absent(),
    required String description,
    this.skill = const Value.absent(),
    required double days,
    required int rate,
    this.otHours = const Value.absent(),
    this.otRate = const Value.absent(),
    required int amount,
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       invoiceId = Value(invoiceId),
       description = Value(description),
       days = Value(days),
       rate = Value(rate),
       amount = Value(amount);
  static Insertable<InvoiceLine> custom({
    Expression<String>? id,
    Expression<String>? invoiceId,
    Expression<String>? labourId,
    Expression<String>? description,
    Expression<String>? skill,
    Expression<double>? days,
    Expression<int>? rate,
    Expression<double>? otHours,
    Expression<int>? otRate,
    Expression<int>? amount,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (invoiceId != null) 'invoice_id': invoiceId,
      if (labourId != null) 'labour_id': labourId,
      if (description != null) 'description': description,
      if (skill != null) 'skill': skill,
      if (days != null) 'days': days,
      if (rate != null) 'rate': rate,
      if (otHours != null) 'ot_hours': otHours,
      if (otRate != null) 'ot_rate': otRate,
      if (amount != null) 'amount': amount,
      if (rowid != null) 'rowid': rowid,
    });
  }

  InvoiceLinesCompanion copyWith({
    Value<String>? id,
    Value<String>? invoiceId,
    Value<String?>? labourId,
    Value<String>? description,
    Value<String>? skill,
    Value<double>? days,
    Value<int>? rate,
    Value<double>? otHours,
    Value<int>? otRate,
    Value<int>? amount,
    Value<int>? rowid,
  }) {
    return InvoiceLinesCompanion(
      id: id ?? this.id,
      invoiceId: invoiceId ?? this.invoiceId,
      labourId: labourId ?? this.labourId,
      description: description ?? this.description,
      skill: skill ?? this.skill,
      days: days ?? this.days,
      rate: rate ?? this.rate,
      otHours: otHours ?? this.otHours,
      otRate: otRate ?? this.otRate,
      amount: amount ?? this.amount,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (invoiceId.present) {
      map['invoice_id'] = Variable<String>(invoiceId.value);
    }
    if (labourId.present) {
      map['labour_id'] = Variable<String>(labourId.value);
    }
    if (description.present) {
      map['description'] = Variable<String>(description.value);
    }
    if (skill.present) {
      map['skill'] = Variable<String>(skill.value);
    }
    if (days.present) {
      map['days'] = Variable<double>(days.value);
    }
    if (rate.present) {
      map['rate'] = Variable<int>(rate.value);
    }
    if (otHours.present) {
      map['ot_hours'] = Variable<double>(otHours.value);
    }
    if (otRate.present) {
      map['ot_rate'] = Variable<int>(otRate.value);
    }
    if (amount.present) {
      map['amount'] = Variable<int>(amount.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('InvoiceLinesCompanion(')
          ..write('id: $id, ')
          ..write('invoiceId: $invoiceId, ')
          ..write('labourId: $labourId, ')
          ..write('description: $description, ')
          ..write('skill: $skill, ')
          ..write('days: $days, ')
          ..write('rate: $rate, ')
          ..write('otHours: $otHours, ')
          ..write('otRate: $otRate, ')
          ..write('amount: $amount, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $InvoicePaymentsTable extends InvoicePayments
    with TableInfo<$InvoicePaymentsTable, InvoicePayment> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $InvoicePaymentsTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _invoiceIdMeta = const VerificationMeta(
    'invoiceId',
  );
  @override
  late final GeneratedColumn<String> invoiceId = GeneratedColumn<String>(
    'invoice_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES invoices (id)',
    ),
  );
  static const VerificationMeta _amountMeta = const VerificationMeta('amount');
  @override
  late final GeneratedColumn<int> amount = GeneratedColumn<int>(
    'amount',
    aliasedName,
    false,
    type: DriftSqlType.int,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _dateMeta = const VerificationMeta('date');
  @override
  late final GeneratedColumn<String> date = GeneratedColumn<String>(
    'date',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _modeMeta = const VerificationMeta('mode');
  @override
  late final GeneratedColumn<String> mode = GeneratedColumn<String>(
    'mode',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant('bank'),
  );
  static const VerificationMeta _referenceMeta = const VerificationMeta(
    'reference',
  );
  @override
  late final GeneratedColumn<String> reference = GeneratedColumn<String>(
    'reference',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _noteMeta = const VerificationMeta('note');
  @override
  late final GeneratedColumn<String> note = GeneratedColumn<String>(
    'note',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _createdAtMeta = const VerificationMeta(
    'createdAt',
  );
  @override
  late final GeneratedColumn<DateTime> createdAt = GeneratedColumn<DateTime>(
    'created_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    invoiceId,
    amount,
    date,
    mode,
    reference,
    note,
    createdAt,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'invoice_payments';
  @override
  VerificationContext validateIntegrity(
    Insertable<InvoicePayment> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('invoice_id')) {
      context.handle(
        _invoiceIdMeta,
        invoiceId.isAcceptableOrUnknown(data['invoice_id']!, _invoiceIdMeta),
      );
    } else if (isInserting) {
      context.missing(_invoiceIdMeta);
    }
    if (data.containsKey('amount')) {
      context.handle(
        _amountMeta,
        amount.isAcceptableOrUnknown(data['amount']!, _amountMeta),
      );
    } else if (isInserting) {
      context.missing(_amountMeta);
    }
    if (data.containsKey('date')) {
      context.handle(
        _dateMeta,
        date.isAcceptableOrUnknown(data['date']!, _dateMeta),
      );
    } else if (isInserting) {
      context.missing(_dateMeta);
    }
    if (data.containsKey('mode')) {
      context.handle(
        _modeMeta,
        mode.isAcceptableOrUnknown(data['mode']!, _modeMeta),
      );
    }
    if (data.containsKey('reference')) {
      context.handle(
        _referenceMeta,
        reference.isAcceptableOrUnknown(data['reference']!, _referenceMeta),
      );
    }
    if (data.containsKey('note')) {
      context.handle(
        _noteMeta,
        note.isAcceptableOrUnknown(data['note']!, _noteMeta),
      );
    }
    if (data.containsKey('created_at')) {
      context.handle(
        _createdAtMeta,
        createdAt.isAcceptableOrUnknown(data['created_at']!, _createdAtMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  InvoicePayment map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return InvoicePayment(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      invoiceId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}invoice_id'],
      )!,
      amount: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}amount'],
      )!,
      date: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}date'],
      )!,
      mode: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}mode'],
      )!,
      reference: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}reference'],
      )!,
      note: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}note'],
      )!,
      createdAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}created_at'],
      )!,
    );
  }

  @override
  $InvoicePaymentsTable createAlias(String alias) {
    return $InvoicePaymentsTable(attachedDatabase, alias);
  }
}

class InvoicePayment extends DataClass implements Insertable<InvoicePayment> {
  final String id;
  final String invoiceId;
  final int amount;
  final String date;
  final String mode;
  final String reference;
  final String note;
  final DateTime createdAt;
  const InvoicePayment({
    required this.id,
    required this.invoiceId,
    required this.amount,
    required this.date,
    required this.mode,
    required this.reference,
    required this.note,
    required this.createdAt,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['invoice_id'] = Variable<String>(invoiceId);
    map['amount'] = Variable<int>(amount);
    map['date'] = Variable<String>(date);
    map['mode'] = Variable<String>(mode);
    map['reference'] = Variable<String>(reference);
    map['note'] = Variable<String>(note);
    map['created_at'] = Variable<DateTime>(createdAt);
    return map;
  }

  InvoicePaymentsCompanion toCompanion(bool nullToAbsent) {
    return InvoicePaymentsCompanion(
      id: Value(id),
      invoiceId: Value(invoiceId),
      amount: Value(amount),
      date: Value(date),
      mode: Value(mode),
      reference: Value(reference),
      note: Value(note),
      createdAt: Value(createdAt),
    );
  }

  factory InvoicePayment.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return InvoicePayment(
      id: serializer.fromJson<String>(json['id']),
      invoiceId: serializer.fromJson<String>(json['invoiceId']),
      amount: serializer.fromJson<int>(json['amount']),
      date: serializer.fromJson<String>(json['date']),
      mode: serializer.fromJson<String>(json['mode']),
      reference: serializer.fromJson<String>(json['reference']),
      note: serializer.fromJson<String>(json['note']),
      createdAt: serializer.fromJson<DateTime>(json['createdAt']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'invoiceId': serializer.toJson<String>(invoiceId),
      'amount': serializer.toJson<int>(amount),
      'date': serializer.toJson<String>(date),
      'mode': serializer.toJson<String>(mode),
      'reference': serializer.toJson<String>(reference),
      'note': serializer.toJson<String>(note),
      'createdAt': serializer.toJson<DateTime>(createdAt),
    };
  }

  InvoicePayment copyWith({
    String? id,
    String? invoiceId,
    int? amount,
    String? date,
    String? mode,
    String? reference,
    String? note,
    DateTime? createdAt,
  }) => InvoicePayment(
    id: id ?? this.id,
    invoiceId: invoiceId ?? this.invoiceId,
    amount: amount ?? this.amount,
    date: date ?? this.date,
    mode: mode ?? this.mode,
    reference: reference ?? this.reference,
    note: note ?? this.note,
    createdAt: createdAt ?? this.createdAt,
  );
  InvoicePayment copyWithCompanion(InvoicePaymentsCompanion data) {
    return InvoicePayment(
      id: data.id.present ? data.id.value : this.id,
      invoiceId: data.invoiceId.present ? data.invoiceId.value : this.invoiceId,
      amount: data.amount.present ? data.amount.value : this.amount,
      date: data.date.present ? data.date.value : this.date,
      mode: data.mode.present ? data.mode.value : this.mode,
      reference: data.reference.present ? data.reference.value : this.reference,
      note: data.note.present ? data.note.value : this.note,
      createdAt: data.createdAt.present ? data.createdAt.value : this.createdAt,
    );
  }

  @override
  String toString() {
    return (StringBuffer('InvoicePayment(')
          ..write('id: $id, ')
          ..write('invoiceId: $invoiceId, ')
          ..write('amount: $amount, ')
          ..write('date: $date, ')
          ..write('mode: $mode, ')
          ..write('reference: $reference, ')
          ..write('note: $note, ')
          ..write('createdAt: $createdAt')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    id,
    invoiceId,
    amount,
    date,
    mode,
    reference,
    note,
    createdAt,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is InvoicePayment &&
          other.id == this.id &&
          other.invoiceId == this.invoiceId &&
          other.amount == this.amount &&
          other.date == this.date &&
          other.mode == this.mode &&
          other.reference == this.reference &&
          other.note == this.note &&
          other.createdAt == this.createdAt);
}

class InvoicePaymentsCompanion extends UpdateCompanion<InvoicePayment> {
  final Value<String> id;
  final Value<String> invoiceId;
  final Value<int> amount;
  final Value<String> date;
  final Value<String> mode;
  final Value<String> reference;
  final Value<String> note;
  final Value<DateTime> createdAt;
  final Value<int> rowid;
  const InvoicePaymentsCompanion({
    this.id = const Value.absent(),
    this.invoiceId = const Value.absent(),
    this.amount = const Value.absent(),
    this.date = const Value.absent(),
    this.mode = const Value.absent(),
    this.reference = const Value.absent(),
    this.note = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  InvoicePaymentsCompanion.insert({
    required String id,
    required String invoiceId,
    required int amount,
    required String date,
    this.mode = const Value.absent(),
    this.reference = const Value.absent(),
    this.note = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       invoiceId = Value(invoiceId),
       amount = Value(amount),
       date = Value(date);
  static Insertable<InvoicePayment> custom({
    Expression<String>? id,
    Expression<String>? invoiceId,
    Expression<int>? amount,
    Expression<String>? date,
    Expression<String>? mode,
    Expression<String>? reference,
    Expression<String>? note,
    Expression<DateTime>? createdAt,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (invoiceId != null) 'invoice_id': invoiceId,
      if (amount != null) 'amount': amount,
      if (date != null) 'date': date,
      if (mode != null) 'mode': mode,
      if (reference != null) 'reference': reference,
      if (note != null) 'note': note,
      if (createdAt != null) 'created_at': createdAt,
      if (rowid != null) 'rowid': rowid,
    });
  }

  InvoicePaymentsCompanion copyWith({
    Value<String>? id,
    Value<String>? invoiceId,
    Value<int>? amount,
    Value<String>? date,
    Value<String>? mode,
    Value<String>? reference,
    Value<String>? note,
    Value<DateTime>? createdAt,
    Value<int>? rowid,
  }) {
    return InvoicePaymentsCompanion(
      id: id ?? this.id,
      invoiceId: invoiceId ?? this.invoiceId,
      amount: amount ?? this.amount,
      date: date ?? this.date,
      mode: mode ?? this.mode,
      reference: reference ?? this.reference,
      note: note ?? this.note,
      createdAt: createdAt ?? this.createdAt,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (invoiceId.present) {
      map['invoice_id'] = Variable<String>(invoiceId.value);
    }
    if (amount.present) {
      map['amount'] = Variable<int>(amount.value);
    }
    if (date.present) {
      map['date'] = Variable<String>(date.value);
    }
    if (mode.present) {
      map['mode'] = Variable<String>(mode.value);
    }
    if (reference.present) {
      map['reference'] = Variable<String>(reference.value);
    }
    if (note.present) {
      map['note'] = Variable<String>(note.value);
    }
    if (createdAt.present) {
      map['created_at'] = Variable<DateTime>(createdAt.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('InvoicePaymentsCompanion(')
          ..write('id: $id, ')
          ..write('invoiceId: $invoiceId, ')
          ..write('amount: $amount, ')
          ..write('date: $date, ')
          ..write('mode: $mode, ')
          ..write('reference: $reference, ')
          ..write('note: $note, ')
          ..write('createdAt: $createdAt, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $AuditLogsTable extends AuditLogs
    with TableInfo<$AuditLogsTable, AuditLog> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $AuditLogsTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<String> id = GeneratedColumn<String>(
    'id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _atMeta = const VerificationMeta('at');
  @override
  late final GeneratedColumn<DateTime> at = GeneratedColumn<DateTime>(
    'at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  static const VerificationMeta _staffIdMeta = const VerificationMeta(
    'staffId',
  );
  @override
  late final GeneratedColumn<String> staffId = GeneratedColumn<String>(
    'staff_id',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _actionMeta = const VerificationMeta('action');
  @override
  late final GeneratedColumn<String> action = GeneratedColumn<String>(
    'action',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _entityMeta = const VerificationMeta('entity');
  @override
  late final GeneratedColumn<String> entity = GeneratedColumn<String>(
    'entity',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _entityIdMeta = const VerificationMeta(
    'entityId',
  );
  @override
  late final GeneratedColumn<String> entityId = GeneratedColumn<String>(
    'entity_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  static const VerificationMeta _detailMeta = const VerificationMeta('detail');
  @override
  late final GeneratedColumn<String> detail = GeneratedColumn<String>(
    'detail',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant(''),
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    at,
    staffId,
    action,
    entity,
    entityId,
    detail,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'audit_logs';
  @override
  VerificationContext validateIntegrity(
    Insertable<AuditLog> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    } else if (isInserting) {
      context.missing(_idMeta);
    }
    if (data.containsKey('at')) {
      context.handle(_atMeta, at.isAcceptableOrUnknown(data['at']!, _atMeta));
    }
    if (data.containsKey('staff_id')) {
      context.handle(
        _staffIdMeta,
        staffId.isAcceptableOrUnknown(data['staff_id']!, _staffIdMeta),
      );
    }
    if (data.containsKey('action')) {
      context.handle(
        _actionMeta,
        action.isAcceptableOrUnknown(data['action']!, _actionMeta),
      );
    } else if (isInserting) {
      context.missing(_actionMeta);
    }
    if (data.containsKey('entity')) {
      context.handle(
        _entityMeta,
        entity.isAcceptableOrUnknown(data['entity']!, _entityMeta),
      );
    } else if (isInserting) {
      context.missing(_entityMeta);
    }
    if (data.containsKey('entity_id')) {
      context.handle(
        _entityIdMeta,
        entityId.isAcceptableOrUnknown(data['entity_id']!, _entityIdMeta),
      );
    }
    if (data.containsKey('detail')) {
      context.handle(
        _detailMeta,
        detail.isAcceptableOrUnknown(data['detail']!, _detailMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  AuditLog map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return AuditLog(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}id'],
      )!,
      at: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}at'],
      )!,
      staffId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}staff_id'],
      ),
      action: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}action'],
      )!,
      entity: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}entity'],
      )!,
      entityId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}entity_id'],
      )!,
      detail: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}detail'],
      )!,
    );
  }

  @override
  $AuditLogsTable createAlias(String alias) {
    return $AuditLogsTable(attachedDatabase, alias);
  }
}

class AuditLog extends DataClass implements Insertable<AuditLog> {
  final String id;
  final DateTime at;
  final String? staffId;
  final String action;
  final String entity;
  final String entityId;
  final String detail;
  const AuditLog({
    required this.id,
    required this.at,
    this.staffId,
    required this.action,
    required this.entity,
    required this.entityId,
    required this.detail,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<String>(id);
    map['at'] = Variable<DateTime>(at);
    if (!nullToAbsent || staffId != null) {
      map['staff_id'] = Variable<String>(staffId);
    }
    map['action'] = Variable<String>(action);
    map['entity'] = Variable<String>(entity);
    map['entity_id'] = Variable<String>(entityId);
    map['detail'] = Variable<String>(detail);
    return map;
  }

  AuditLogsCompanion toCompanion(bool nullToAbsent) {
    return AuditLogsCompanion(
      id: Value(id),
      at: Value(at),
      staffId: staffId == null && nullToAbsent
          ? const Value.absent()
          : Value(staffId),
      action: Value(action),
      entity: Value(entity),
      entityId: Value(entityId),
      detail: Value(detail),
    );
  }

  factory AuditLog.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return AuditLog(
      id: serializer.fromJson<String>(json['id']),
      at: serializer.fromJson<DateTime>(json['at']),
      staffId: serializer.fromJson<String?>(json['staffId']),
      action: serializer.fromJson<String>(json['action']),
      entity: serializer.fromJson<String>(json['entity']),
      entityId: serializer.fromJson<String>(json['entityId']),
      detail: serializer.fromJson<String>(json['detail']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<String>(id),
      'at': serializer.toJson<DateTime>(at),
      'staffId': serializer.toJson<String?>(staffId),
      'action': serializer.toJson<String>(action),
      'entity': serializer.toJson<String>(entity),
      'entityId': serializer.toJson<String>(entityId),
      'detail': serializer.toJson<String>(detail),
    };
  }

  AuditLog copyWith({
    String? id,
    DateTime? at,
    Value<String?> staffId = const Value.absent(),
    String? action,
    String? entity,
    String? entityId,
    String? detail,
  }) => AuditLog(
    id: id ?? this.id,
    at: at ?? this.at,
    staffId: staffId.present ? staffId.value : this.staffId,
    action: action ?? this.action,
    entity: entity ?? this.entity,
    entityId: entityId ?? this.entityId,
    detail: detail ?? this.detail,
  );
  AuditLog copyWithCompanion(AuditLogsCompanion data) {
    return AuditLog(
      id: data.id.present ? data.id.value : this.id,
      at: data.at.present ? data.at.value : this.at,
      staffId: data.staffId.present ? data.staffId.value : this.staffId,
      action: data.action.present ? data.action.value : this.action,
      entity: data.entity.present ? data.entity.value : this.entity,
      entityId: data.entityId.present ? data.entityId.value : this.entityId,
      detail: data.detail.present ? data.detail.value : this.detail,
    );
  }

  @override
  String toString() {
    return (StringBuffer('AuditLog(')
          ..write('id: $id, ')
          ..write('at: $at, ')
          ..write('staffId: $staffId, ')
          ..write('action: $action, ')
          ..write('entity: $entity, ')
          ..write('entityId: $entityId, ')
          ..write('detail: $detail')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode =>
      Object.hash(id, at, staffId, action, entity, entityId, detail);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is AuditLog &&
          other.id == this.id &&
          other.at == this.at &&
          other.staffId == this.staffId &&
          other.action == this.action &&
          other.entity == this.entity &&
          other.entityId == this.entityId &&
          other.detail == this.detail);
}

class AuditLogsCompanion extends UpdateCompanion<AuditLog> {
  final Value<String> id;
  final Value<DateTime> at;
  final Value<String?> staffId;
  final Value<String> action;
  final Value<String> entity;
  final Value<String> entityId;
  final Value<String> detail;
  final Value<int> rowid;
  const AuditLogsCompanion({
    this.id = const Value.absent(),
    this.at = const Value.absent(),
    this.staffId = const Value.absent(),
    this.action = const Value.absent(),
    this.entity = const Value.absent(),
    this.entityId = const Value.absent(),
    this.detail = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  AuditLogsCompanion.insert({
    required String id,
    this.at = const Value.absent(),
    this.staffId = const Value.absent(),
    required String action,
    required String entity,
    this.entityId = const Value.absent(),
    this.detail = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : id = Value(id),
       action = Value(action),
       entity = Value(entity);
  static Insertable<AuditLog> custom({
    Expression<String>? id,
    Expression<DateTime>? at,
    Expression<String>? staffId,
    Expression<String>? action,
    Expression<String>? entity,
    Expression<String>? entityId,
    Expression<String>? detail,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (at != null) 'at': at,
      if (staffId != null) 'staff_id': staffId,
      if (action != null) 'action': action,
      if (entity != null) 'entity': entity,
      if (entityId != null) 'entity_id': entityId,
      if (detail != null) 'detail': detail,
      if (rowid != null) 'rowid': rowid,
    });
  }

  AuditLogsCompanion copyWith({
    Value<String>? id,
    Value<DateTime>? at,
    Value<String?>? staffId,
    Value<String>? action,
    Value<String>? entity,
    Value<String>? entityId,
    Value<String>? detail,
    Value<int>? rowid,
  }) {
    return AuditLogsCompanion(
      id: id ?? this.id,
      at: at ?? this.at,
      staffId: staffId ?? this.staffId,
      action: action ?? this.action,
      entity: entity ?? this.entity,
      entityId: entityId ?? this.entityId,
      detail: detail ?? this.detail,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<String>(id.value);
    }
    if (at.present) {
      map['at'] = Variable<DateTime>(at.value);
    }
    if (staffId.present) {
      map['staff_id'] = Variable<String>(staffId.value);
    }
    if (action.present) {
      map['action'] = Variable<String>(action.value);
    }
    if (entity.present) {
      map['entity'] = Variable<String>(entity.value);
    }
    if (entityId.present) {
      map['entity_id'] = Variable<String>(entityId.value);
    }
    if (detail.present) {
      map['detail'] = Variable<String>(detail.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('AuditLogsCompanion(')
          ..write('id: $id, ')
          ..write('at: $at, ')
          ..write('staffId: $staffId, ')
          ..write('action: $action, ')
          ..write('entity: $entity, ')
          ..write('entityId: $entityId, ')
          ..write('detail: $detail, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $KeyValuesTable extends KeyValues
    with TableInfo<$KeyValuesTable, KeyValue> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $KeyValuesTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _keyMeta = const VerificationMeta('key');
  @override
  late final GeneratedColumn<String> key = GeneratedColumn<String>(
    'key',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _valueMeta = const VerificationMeta('value');
  @override
  late final GeneratedColumn<String> value = GeneratedColumn<String>(
    'value',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  @override
  List<GeneratedColumn> get $columns => [key, value];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'key_values';
  @override
  VerificationContext validateIntegrity(
    Insertable<KeyValue> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('key')) {
      context.handle(
        _keyMeta,
        key.isAcceptableOrUnknown(data['key']!, _keyMeta),
      );
    } else if (isInserting) {
      context.missing(_keyMeta);
    }
    if (data.containsKey('value')) {
      context.handle(
        _valueMeta,
        value.isAcceptableOrUnknown(data['value']!, _valueMeta),
      );
    } else if (isInserting) {
      context.missing(_valueMeta);
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {key};
  @override
  KeyValue map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return KeyValue(
      key: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}key'],
      )!,
      value: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}value'],
      )!,
    );
  }

  @override
  $KeyValuesTable createAlias(String alias) {
    return $KeyValuesTable(attachedDatabase, alias);
  }
}

class KeyValue extends DataClass implements Insertable<KeyValue> {
  final String key;
  final String value;
  const KeyValue({required this.key, required this.value});
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['key'] = Variable<String>(key);
    map['value'] = Variable<String>(value);
    return map;
  }

  KeyValuesCompanion toCompanion(bool nullToAbsent) {
    return KeyValuesCompanion(key: Value(key), value: Value(value));
  }

  factory KeyValue.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return KeyValue(
      key: serializer.fromJson<String>(json['key']),
      value: serializer.fromJson<String>(json['value']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'key': serializer.toJson<String>(key),
      'value': serializer.toJson<String>(value),
    };
  }

  KeyValue copyWith({String? key, String? value}) =>
      KeyValue(key: key ?? this.key, value: value ?? this.value);
  KeyValue copyWithCompanion(KeyValuesCompanion data) {
    return KeyValue(
      key: data.key.present ? data.key.value : this.key,
      value: data.value.present ? data.value.value : this.value,
    );
  }

  @override
  String toString() {
    return (StringBuffer('KeyValue(')
          ..write('key: $key, ')
          ..write('value: $value')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(key, value);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is KeyValue && other.key == this.key && other.value == this.value);
}

class KeyValuesCompanion extends UpdateCompanion<KeyValue> {
  final Value<String> key;
  final Value<String> value;
  final Value<int> rowid;
  const KeyValuesCompanion({
    this.key = const Value.absent(),
    this.value = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  KeyValuesCompanion.insert({
    required String key,
    required String value,
    this.rowid = const Value.absent(),
  }) : key = Value(key),
       value = Value(value);
  static Insertable<KeyValue> custom({
    Expression<String>? key,
    Expression<String>? value,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (key != null) 'key': key,
      if (value != null) 'value': value,
      if (rowid != null) 'rowid': rowid,
    });
  }

  KeyValuesCompanion copyWith({
    Value<String>? key,
    Value<String>? value,
    Value<int>? rowid,
  }) {
    return KeyValuesCompanion(
      key: key ?? this.key,
      value: value ?? this.value,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (key.present) {
      map['key'] = Variable<String>(key.value);
    }
    if (value.present) {
      map['value'] = Variable<String>(value.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('KeyValuesCompanion(')
          ..write('key: $key, ')
          ..write('value: $value, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

abstract class _$AppDatabase extends GeneratedDatabase {
  _$AppDatabase(QueryExecutor e) : super(e);
  $AppDatabaseManager get managers => $AppDatabaseManager(this);
  late final $BranchesTable branches = $BranchesTable(this);
  late final $RolesTable roles = $RolesTable(this);
  late final $StaffMembersTable staffMembers = $StaffMembersTable(this);
  late final $CompaniesTable companies = $CompaniesTable(this);
  late final $SitesTable sites = $SitesTable(this);
  late final $ContractsTable contracts = $ContractsTable(this);
  late final $ContractRatesTable contractRates = $ContractRatesTable(this);
  late final $LaboursTable labours = $LaboursTable(this);
  late final $LabourRatesTable labourRates = $LabourRatesTable(this);
  late final $LabourDocumentsTable labourDocuments = $LabourDocumentsTable(
    this,
  );
  late final $AssignmentsTable assignments = $AssignmentsTable(this);
  late final $AttendanceTable attendance = $AttendanceTable(this);
  late final $AttendanceSheetsTable attendanceSheets = $AttendanceSheetsTable(
    this,
  );
  late final $PaymentsTable payments = $PaymentsTable(this);
  late final $InvoicesTable invoices = $InvoicesTable(this);
  late final $InvoiceLinesTable invoiceLines = $InvoiceLinesTable(this);
  late final $InvoicePaymentsTable invoicePayments = $InvoicePaymentsTable(
    this,
  );
  late final $AuditLogsTable auditLogs = $AuditLogsTable(this);
  late final $KeyValuesTable keyValues = $KeyValuesTable(this);
  @override
  Iterable<TableInfo<Table, Object?>> get allTables =>
      allSchemaEntities.whereType<TableInfo<Table, Object?>>();
  @override
  List<DatabaseSchemaEntity> get allSchemaEntities => [
    branches,
    roles,
    staffMembers,
    companies,
    sites,
    contracts,
    contractRates,
    labours,
    labourRates,
    labourDocuments,
    assignments,
    attendance,
    attendanceSheets,
    payments,
    invoices,
    invoiceLines,
    invoicePayments,
    auditLogs,
    keyValues,
  ];
}

typedef $$BranchesTableCreateCompanionBuilder = BranchesCompanion Function({
  required String id,
  required String name,
  Value<String> address,
  Value<String> city,
  Value<String> mobile,
  Value<bool> isActive,
  Value<DateTime> createdAt,
  Value<int> rowid,
});
typedef $$BranchesTableUpdateCompanionBuilder = BranchesCompanion Function({
  Value<String> id,
  Value<String> name,
  Value<String> address,
  Value<String> city,
  Value<String> mobile,
  Value<bool> isActive,
  Value<DateTime> createdAt,
  Value<int> rowid,
});

final class $$BranchesTableReferences
    extends BaseReferences<_$AppDatabase, $BranchesTable, Branch> {
  $$BranchesTableReferences(super.$_db, super.$_table, super.$_typedResult);

  static MultiTypedResultKey<$CompaniesTable, List<Company>>
  _companiesRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.companies,
    aliasName: 'branches__id__companies__branch_id',
  );

  $$CompaniesTableProcessedTableManager get companiesRefs {
    final manager = $$CompaniesTableTableManager(
      $_db,
      $_db.companies,
    ).filter((f) => f.branchId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_companiesRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }

  static MultiTypedResultKey<$LaboursTable, List<Labour>> _laboursRefsTable(
    _$AppDatabase db,
  ) => MultiTypedResultKey.fromTable(
    db.labours,
    aliasName: 'branches__id__labours__branch_id',
  );

  $$LaboursTableProcessedTableManager get laboursRefs {
    final manager = $$LaboursTableTableManager(
      $_db,
      $_db.labours,
    ).filter((f) => f.branchId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_laboursRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }

  static MultiTypedResultKey<$PaymentsTable, List<Payment>> _paymentsRefsTable(
    _$AppDatabase db,
  ) => MultiTypedResultKey.fromTable(
    db.payments,
    aliasName: 'branches__id__payments__branch_id',
  );

  $$PaymentsTableProcessedTableManager get paymentsRefs {
    final manager = $$PaymentsTableTableManager(
      $_db,
      $_db.payments,
    ).filter((f) => f.branchId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_paymentsRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }

  static MultiTypedResultKey<$InvoicesTable, List<Invoice>> _invoicesRefsTable(
    _$AppDatabase db,
  ) => MultiTypedResultKey.fromTable(
    db.invoices,
    aliasName: 'branches__id__invoices__branch_id',
  );

  $$InvoicesTableProcessedTableManager get invoicesRefs {
    final manager = $$InvoicesTableTableManager(
      $_db,
      $_db.invoices,
    ).filter((f) => f.branchId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_invoicesRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }
}

class $$BranchesTableFilterComposer
    extends Composer<_$AppDatabase, $BranchesTable> {
  $$BranchesTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get name => $composableBuilder(
    column: $table.name,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get address => $composableBuilder(
    column: $table.address,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get city => $composableBuilder(
    column: $table.city,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get mobile => $composableBuilder(
    column: $table.mobile,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<bool> get isActive => $composableBuilder(
    column: $table.isActive,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnFilters(column),
  );

  Expression<bool> companiesRefs(
    Expression<bool> Function($$CompaniesTableFilterComposer f) f,
  ) {
    final $$CompaniesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.companies,
      getReferencedColumn: (t) => t.branchId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$CompaniesTableFilterComposer(
            $db: $db,
            $table: $db.companies,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<bool> laboursRefs(
    Expression<bool> Function($$LaboursTableFilterComposer f) f,
  ) {
    final $$LaboursTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.branchId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableFilterComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<bool> paymentsRefs(
    Expression<bool> Function($$PaymentsTableFilterComposer f) f,
  ) {
    final $$PaymentsTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.payments,
      getReferencedColumn: (t) => t.branchId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$PaymentsTableFilterComposer(
            $db: $db,
            $table: $db.payments,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<bool> invoicesRefs(
    Expression<bool> Function($$InvoicesTableFilterComposer f) f,
  ) {
    final $$InvoicesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.invoices,
      getReferencedColumn: (t) => t.branchId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoicesTableFilterComposer(
            $db: $db,
            $table: $db.invoices,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$BranchesTableOrderingComposer
    extends Composer<_$AppDatabase, $BranchesTable> {
  $$BranchesTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get name => $composableBuilder(
    column: $table.name,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get address => $composableBuilder(
    column: $table.address,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get city => $composableBuilder(
    column: $table.city,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get mobile => $composableBuilder(
    column: $table.mobile,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<bool> get isActive => $composableBuilder(
    column: $table.isActive,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnOrderings(column),
  );
}

class $$BranchesTableAnnotationComposer
    extends Composer<_$AppDatabase, $BranchesTable> {
  $$BranchesTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get name =>
      $composableBuilder(column: $table.name, builder: (column) => column);

  GeneratedColumn<String> get address =>
      $composableBuilder(column: $table.address, builder: (column) => column);

  GeneratedColumn<String> get city =>
      $composableBuilder(column: $table.city, builder: (column) => column);

  GeneratedColumn<String> get mobile =>
      $composableBuilder(column: $table.mobile, builder: (column) => column);

  GeneratedColumn<bool> get isActive =>
      $composableBuilder(column: $table.isActive, builder: (column) => column);

  GeneratedColumn<DateTime> get createdAt =>
      $composableBuilder(column: $table.createdAt, builder: (column) => column);

  Expression<T> companiesRefs<T extends Object>(
    Expression<T> Function($$CompaniesTableAnnotationComposer a) f,
  ) {
    final $$CompaniesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.companies,
      getReferencedColumn: (t) => t.branchId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$CompaniesTableAnnotationComposer(
            $db: $db,
            $table: $db.companies,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<T> laboursRefs<T extends Object>(
    Expression<T> Function($$LaboursTableAnnotationComposer a) f,
  ) {
    final $$LaboursTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.branchId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableAnnotationComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<T> paymentsRefs<T extends Object>(
    Expression<T> Function($$PaymentsTableAnnotationComposer a) f,
  ) {
    final $$PaymentsTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.payments,
      getReferencedColumn: (t) => t.branchId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$PaymentsTableAnnotationComposer(
            $db: $db,
            $table: $db.payments,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<T> invoicesRefs<T extends Object>(
    Expression<T> Function($$InvoicesTableAnnotationComposer a) f,
  ) {
    final $$InvoicesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.invoices,
      getReferencedColumn: (t) => t.branchId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoicesTableAnnotationComposer(
            $db: $db,
            $table: $db.invoices,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$BranchesTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $BranchesTable,
          Branch,
          $$BranchesTableFilterComposer,
          $$BranchesTableOrderingComposer,
          $$BranchesTableAnnotationComposer,
          $$BranchesTableCreateCompanionBuilder,
          $$BranchesTableUpdateCompanionBuilder,
          (Branch, $$BranchesTableReferences),
          Branch,
          PrefetchHooks Function({
            bool companiesRefs,
            bool laboursRefs,
            bool paymentsRefs,
            bool invoicesRefs,
          })
        > {
  $$BranchesTableTableManager(_$AppDatabase db, $BranchesTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$BranchesTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$BranchesTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$BranchesTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> name = const Value.absent(),
                Value<String> address = const Value.absent(),
                Value<String> city = const Value.absent(),
                Value<String> mobile = const Value.absent(),
                Value<bool> isActive = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => BranchesCompanion(
                id: id,
                name: name,
                address: address,
                city: city,
                mobile: mobile,
                isActive: isActive,
                createdAt: createdAt,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String name,
                Value<String> address = const Value.absent(),
                Value<String> city = const Value.absent(),
                Value<String> mobile = const Value.absent(),
                Value<bool> isActive = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => BranchesCompanion.insert(
                id: id,
                name: name,
                address: address,
                city: city,
                mobile: mobile,
                isActive: isActive,
                createdAt: createdAt,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$BranchesTable, Branch>(table),
                  $$BranchesTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback:
              ({
                companiesRefs = false,
                laboursRefs = false,
                paymentsRefs = false,
                invoicesRefs = false,
              }) {
                return PrefetchHooks(
                  db: db,
                  explicitlyWatchedTables: [
                    if (companiesRefs) db.companies,
                    if (laboursRefs) db.labours,
                    if (paymentsRefs) db.payments,
                    if (invoicesRefs) db.invoices,
                  ],
                  addJoins: null,
                  getPrefetchedDataCallback: (items) async {
                    return [
                      if (companiesRefs)
                        await $_getPrefetchedData<
                          Branch,
                          $BranchesTable,
                          Company
                        >(
                          currentTable: table,
                          referencedTable: $$BranchesTableReferences
                              ._companiesRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$BranchesTableReferences(
                                db,
                                table,
                                p0,
                              ).companiesRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.branchId == item.id,
                              ),
                          typedResults: items,
                        ),
                      if (laboursRefs)
                        await $_getPrefetchedData<
                          Branch,
                          $BranchesTable,
                          Labour
                        >(
                          currentTable: table,
                          referencedTable: $$BranchesTableReferences
                              ._laboursRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$BranchesTableReferences(
                                db,
                                table,
                                p0,
                              ).laboursRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.branchId == item.id,
                              ),
                          typedResults: items,
                        ),
                      if (paymentsRefs)
                        await $_getPrefetchedData<
                          Branch,
                          $BranchesTable,
                          Payment
                        >(
                          currentTable: table,
                          referencedTable: $$BranchesTableReferences
                              ._paymentsRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$BranchesTableReferences(
                                db,
                                table,
                                p0,
                              ).paymentsRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.branchId == item.id,
                              ),
                          typedResults: items,
                        ),
                      if (invoicesRefs)
                        await $_getPrefetchedData<
                          Branch,
                          $BranchesTable,
                          Invoice
                        >(
                          currentTable: table,
                          referencedTable: $$BranchesTableReferences
                              ._invoicesRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$BranchesTableReferences(
                                db,
                                table,
                                p0,
                              ).invoicesRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.branchId == item.id,
                              ),
                          typedResults: items,
                        ),
                    ];
                  },
                );
              },
        ),
      );
}

typedef $$BranchesTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $BranchesTable,
      Branch,
      $$BranchesTableFilterComposer,
      $$BranchesTableOrderingComposer,
      $$BranchesTableAnnotationComposer,
      $$BranchesTableCreateCompanionBuilder,
      $$BranchesTableUpdateCompanionBuilder,
      (Branch, $$BranchesTableReferences),
      Branch,
      PrefetchHooks Function({
        bool companiesRefs,
        bool laboursRefs,
        bool paymentsRefs,
        bool invoicesRefs,
      })
    >;
typedef $$RolesTableCreateCompanionBuilder = RolesCompanion Function({
  required String id,
  required String name,
  Value<String> permissions,
  Value<bool> isSystem,
  Value<int> rowid,
});
typedef $$RolesTableUpdateCompanionBuilder = RolesCompanion Function({
  Value<String> id,
  Value<String> name,
  Value<String> permissions,
  Value<bool> isSystem,
  Value<int> rowid,
});

final class $$RolesTableReferences
    extends BaseReferences<_$AppDatabase, $RolesTable, Role> {
  $$RolesTableReferences(super.$_db, super.$_table, super.$_typedResult);

  static MultiTypedResultKey<$StaffMembersTable, List<StaffMember>>
  _staffMembersRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.staffMembers,
    aliasName: 'roles__id__staff_members__role_id',
  );

  $$StaffMembersTableProcessedTableManager get staffMembersRefs {
    final manager = $$StaffMembersTableTableManager(
      $_db,
      $_db.staffMembers,
    ).filter((f) => f.roleId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_staffMembersRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }
}

class $$RolesTableFilterComposer extends Composer<_$AppDatabase, $RolesTable> {
  $$RolesTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get name => $composableBuilder(
    column: $table.name,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get permissions => $composableBuilder(
    column: $table.permissions,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<bool> get isSystem => $composableBuilder(
    column: $table.isSystem,
    builder: (column) => ColumnFilters(column),
  );

  Expression<bool> staffMembersRefs(
    Expression<bool> Function($$StaffMembersTableFilterComposer f) f,
  ) {
    final $$StaffMembersTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.staffMembers,
      getReferencedColumn: (t) => t.roleId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$StaffMembersTableFilterComposer(
            $db: $db,
            $table: $db.staffMembers,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$RolesTableOrderingComposer
    extends Composer<_$AppDatabase, $RolesTable> {
  $$RolesTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get name => $composableBuilder(
    column: $table.name,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get permissions => $composableBuilder(
    column: $table.permissions,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<bool> get isSystem => $composableBuilder(
    column: $table.isSystem,
    builder: (column) => ColumnOrderings(column),
  );
}

class $$RolesTableAnnotationComposer
    extends Composer<_$AppDatabase, $RolesTable> {
  $$RolesTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get name =>
      $composableBuilder(column: $table.name, builder: (column) => column);

  GeneratedColumn<String> get permissions => $composableBuilder(
    column: $table.permissions,
    builder: (column) => column,
  );

  GeneratedColumn<bool> get isSystem =>
      $composableBuilder(column: $table.isSystem, builder: (column) => column);

  Expression<T> staffMembersRefs<T extends Object>(
    Expression<T> Function($$StaffMembersTableAnnotationComposer a) f,
  ) {
    final $$StaffMembersTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.staffMembers,
      getReferencedColumn: (t) => t.roleId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$StaffMembersTableAnnotationComposer(
            $db: $db,
            $table: $db.staffMembers,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$RolesTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $RolesTable,
          Role,
          $$RolesTableFilterComposer,
          $$RolesTableOrderingComposer,
          $$RolesTableAnnotationComposer,
          $$RolesTableCreateCompanionBuilder,
          $$RolesTableUpdateCompanionBuilder,
          (Role, $$RolesTableReferences),
          Role,
          PrefetchHooks Function({bool staffMembersRefs})
        > {
  $$RolesTableTableManager(_$AppDatabase db, $RolesTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$RolesTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$RolesTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$RolesTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> name = const Value.absent(),
                Value<String> permissions = const Value.absent(),
                Value<bool> isSystem = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => RolesCompanion(
                id: id,
                name: name,
                permissions: permissions,
                isSystem: isSystem,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String name,
                Value<String> permissions = const Value.absent(),
                Value<bool> isSystem = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => RolesCompanion.insert(
                id: id,
                name: name,
                permissions: permissions,
                isSystem: isSystem,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$RolesTable, Role>(table),
                  $$RolesTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback: ({staffMembersRefs = false}) {
            return PrefetchHooks(
              db: db,
              explicitlyWatchedTables: [if (staffMembersRefs) db.staffMembers],
              addJoins: null,
              getPrefetchedDataCallback: (items) async {
                return [
                  if (staffMembersRefs)
                    await $_getPrefetchedData<Role, $RolesTable, StaffMember>(
                      currentTable: table,
                      referencedTable: $$RolesTableReferences
                          ._staffMembersRefsTable(db),
                      managerFromTypedResult: (p0) => $$RolesTableReferences(
                        db,
                        table,
                        p0,
                      ).staffMembersRefs,
                      referencedItemsForCurrentItem: (item, referencedItems) =>
                          referencedItems.where((e) => e.roleId == item.id),
                      typedResults: items,
                    ),
                ];
              },
            );
          },
        ),
      );
}

typedef $$RolesTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $RolesTable,
      Role,
      $$RolesTableFilterComposer,
      $$RolesTableOrderingComposer,
      $$RolesTableAnnotationComposer,
      $$RolesTableCreateCompanionBuilder,
      $$RolesTableUpdateCompanionBuilder,
      (Role, $$RolesTableReferences),
      Role,
      PrefetchHooks Function({bool staffMembersRefs})
    >;
typedef $$StaffMembersTableCreateCompanionBuilder =
    StaffMembersCompanion Function({
      required String id,
      required String name,
      Value<String> mobile,
      Value<String> email,
      required String roleId,
      required String secretHash,
      required String salt,
      Value<bool> isActive,
      Value<String> branchIds,
      Value<String> siteIds,
      Value<bool> emailVerified,
      Value<DateTime> createdAt,
      Value<int> rowid,
    });
typedef $$StaffMembersTableUpdateCompanionBuilder =
    StaffMembersCompanion Function({
      Value<String> id,
      Value<String> name,
      Value<String> mobile,
      Value<String> email,
      Value<String> roleId,
      Value<String> secretHash,
      Value<String> salt,
      Value<bool> isActive,
      Value<String> branchIds,
      Value<String> siteIds,
      Value<bool> emailVerified,
      Value<DateTime> createdAt,
      Value<int> rowid,
    });

final class $$StaffMembersTableReferences
    extends BaseReferences<_$AppDatabase, $StaffMembersTable, StaffMember> {
  $$StaffMembersTableReferences(super.$_db, super.$_table, super.$_typedResult);

  static $RolesTable _roleIdTable(_$AppDatabase db) =>
      db.roles.createAlias('staff_members__role_id__roles__id');

  $$RolesTableProcessedTableManager get roleId {
    final $_column = $_itemColumn<String>('role_id')!;

    final manager = $$RolesTableTableManager(
      $_db,
      $_db.roles,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_roleIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }
}

class $$StaffMembersTableFilterComposer
    extends Composer<_$AppDatabase, $StaffMembersTable> {
  $$StaffMembersTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get name => $composableBuilder(
    column: $table.name,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get mobile => $composableBuilder(
    column: $table.mobile,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get email => $composableBuilder(
    column: $table.email,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get secretHash => $composableBuilder(
    column: $table.secretHash,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get salt => $composableBuilder(
    column: $table.salt,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<bool> get isActive => $composableBuilder(
    column: $table.isActive,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get branchIds => $composableBuilder(
    column: $table.branchIds,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get siteIds => $composableBuilder(
    column: $table.siteIds,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<bool> get emailVerified => $composableBuilder(
    column: $table.emailVerified,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnFilters(column),
  );

  $$RolesTableFilterComposer get roleId {
    final $$RolesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.roleId,
      referencedTable: $db.roles,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$RolesTableFilterComposer(
            $db: $db,
            $table: $db.roles,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$StaffMembersTableOrderingComposer
    extends Composer<_$AppDatabase, $StaffMembersTable> {
  $$StaffMembersTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get name => $composableBuilder(
    column: $table.name,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get mobile => $composableBuilder(
    column: $table.mobile,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get email => $composableBuilder(
    column: $table.email,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get secretHash => $composableBuilder(
    column: $table.secretHash,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get salt => $composableBuilder(
    column: $table.salt,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<bool> get isActive => $composableBuilder(
    column: $table.isActive,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get branchIds => $composableBuilder(
    column: $table.branchIds,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get siteIds => $composableBuilder(
    column: $table.siteIds,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<bool> get emailVerified => $composableBuilder(
    column: $table.emailVerified,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnOrderings(column),
  );

  $$RolesTableOrderingComposer get roleId {
    final $$RolesTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.roleId,
      referencedTable: $db.roles,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$RolesTableOrderingComposer(
            $db: $db,
            $table: $db.roles,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$StaffMembersTableAnnotationComposer
    extends Composer<_$AppDatabase, $StaffMembersTable> {
  $$StaffMembersTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get name =>
      $composableBuilder(column: $table.name, builder: (column) => column);

  GeneratedColumn<String> get mobile =>
      $composableBuilder(column: $table.mobile, builder: (column) => column);

  GeneratedColumn<String> get email =>
      $composableBuilder(column: $table.email, builder: (column) => column);

  GeneratedColumn<String> get secretHash => $composableBuilder(
    column: $table.secretHash,
    builder: (column) => column,
  );

  GeneratedColumn<String> get salt =>
      $composableBuilder(column: $table.salt, builder: (column) => column);

  GeneratedColumn<bool> get isActive =>
      $composableBuilder(column: $table.isActive, builder: (column) => column);

  GeneratedColumn<String> get branchIds =>
      $composableBuilder(column: $table.branchIds, builder: (column) => column);

  GeneratedColumn<String> get siteIds =>
      $composableBuilder(column: $table.siteIds, builder: (column) => column);

  GeneratedColumn<bool> get emailVerified => $composableBuilder(
    column: $table.emailVerified,
    builder: (column) => column,
  );

  GeneratedColumn<DateTime> get createdAt =>
      $composableBuilder(column: $table.createdAt, builder: (column) => column);

  $$RolesTableAnnotationComposer get roleId {
    final $$RolesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.roleId,
      referencedTable: $db.roles,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$RolesTableAnnotationComposer(
            $db: $db,
            $table: $db.roles,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$StaffMembersTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $StaffMembersTable,
          StaffMember,
          $$StaffMembersTableFilterComposer,
          $$StaffMembersTableOrderingComposer,
          $$StaffMembersTableAnnotationComposer,
          $$StaffMembersTableCreateCompanionBuilder,
          $$StaffMembersTableUpdateCompanionBuilder,
          (StaffMember, $$StaffMembersTableReferences),
          StaffMember,
          PrefetchHooks Function({bool roleId})
        > {
  $$StaffMembersTableTableManager(_$AppDatabase db, $StaffMembersTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$StaffMembersTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$StaffMembersTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$StaffMembersTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> name = const Value.absent(),
                Value<String> mobile = const Value.absent(),
                Value<String> email = const Value.absent(),
                Value<String> roleId = const Value.absent(),
                Value<String> secretHash = const Value.absent(),
                Value<String> salt = const Value.absent(),
                Value<bool> isActive = const Value.absent(),
                Value<String> branchIds = const Value.absent(),
                Value<String> siteIds = const Value.absent(),
                Value<bool> emailVerified = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => StaffMembersCompanion(
                id: id,
                name: name,
                mobile: mobile,
                email: email,
                roleId: roleId,
                secretHash: secretHash,
                salt: salt,
                isActive: isActive,
                branchIds: branchIds,
                siteIds: siteIds,
                emailVerified: emailVerified,
                createdAt: createdAt,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String name,
                Value<String> mobile = const Value.absent(),
                Value<String> email = const Value.absent(),
                required String roleId,
                required String secretHash,
                required String salt,
                Value<bool> isActive = const Value.absent(),
                Value<String> branchIds = const Value.absent(),
                Value<String> siteIds = const Value.absent(),
                Value<bool> emailVerified = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => StaffMembersCompanion.insert(
                id: id,
                name: name,
                mobile: mobile,
                email: email,
                roleId: roleId,
                secretHash: secretHash,
                salt: salt,
                isActive: isActive,
                branchIds: branchIds,
                siteIds: siteIds,
                emailVerified: emailVerified,
                createdAt: createdAt,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$StaffMembersTable, StaffMember>(table),
                  $$StaffMembersTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback: ({roleId = false}) {
            return PrefetchHooks(
              db: db,
              explicitlyWatchedTables: [],
              addJoins:
                  <
                    T extends TableManagerState<
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic
                    >
                  >(state) {
                    if (roleId) {
                      state = state.withJoin(
                        currentTable: table,
                        currentColumn: table.roleId,
                        referencedTable: $$StaffMembersTableReferences
                            ._roleIdTable(db),
                        referencedColumn: $$StaffMembersTableReferences
                            ._roleIdTable(db)
                            .id,
                      ) as T;
                    }

                    return state;
                  },
              getPrefetchedDataCallback: (items) async {
                return [];
              },
            );
          },
        ),
      );
}

typedef $$StaffMembersTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $StaffMembersTable,
      StaffMember,
      $$StaffMembersTableFilterComposer,
      $$StaffMembersTableOrderingComposer,
      $$StaffMembersTableAnnotationComposer,
      $$StaffMembersTableCreateCompanionBuilder,
      $$StaffMembersTableUpdateCompanionBuilder,
      (StaffMember, $$StaffMembersTableReferences),
      StaffMember,
      PrefetchHooks Function({bool roleId})
    >;
typedef $$CompaniesTableCreateCompanionBuilder = CompaniesCompanion Function({
  required String id,
  required String branchId,
  required String name,
  Value<String> contactPerson,
  Value<String> mobile,
  Value<String> email,
  Value<String> address,
  Value<String> gstin,
  Value<bool> isActive,
  Value<DateTime> createdAt,
  Value<int> rowid,
});
typedef $$CompaniesTableUpdateCompanionBuilder = CompaniesCompanion Function({
  Value<String> id,
  Value<String> branchId,
  Value<String> name,
  Value<String> contactPerson,
  Value<String> mobile,
  Value<String> email,
  Value<String> address,
  Value<String> gstin,
  Value<bool> isActive,
  Value<DateTime> createdAt,
  Value<int> rowid,
});

final class $$CompaniesTableReferences
    extends BaseReferences<_$AppDatabase, $CompaniesTable, Company> {
  $$CompaniesTableReferences(super.$_db, super.$_table, super.$_typedResult);

  static $BranchesTable _branchIdTable(_$AppDatabase db) =>
      db.branches.createAlias('companies__branch_id__branches__id');

  $$BranchesTableProcessedTableManager get branchId {
    final $_column = $_itemColumn<String>('branch_id')!;

    final manager = $$BranchesTableTableManager(
      $_db,
      $_db.branches,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_branchIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }

  static MultiTypedResultKey<$SitesTable, List<Site>> _sitesRefsTable(
    _$AppDatabase db,
  ) => MultiTypedResultKey.fromTable(
    db.sites,
    aliasName: 'companies__id__sites__company_id',
  );

  $$SitesTableProcessedTableManager get sitesRefs {
    final manager = $$SitesTableTableManager(
      $_db,
      $_db.sites,
    ).filter((f) => f.companyId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_sitesRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }

  static MultiTypedResultKey<$ContractsTable, List<Contract>>
  _contractsRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.contracts,
    aliasName: 'companies__id__contracts__company_id',
  );

  $$ContractsTableProcessedTableManager get contractsRefs {
    final manager = $$ContractsTableTableManager(
      $_db,
      $_db.contracts,
    ).filter((f) => f.companyId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_contractsRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }

  static MultiTypedResultKey<$InvoicesTable, List<Invoice>> _invoicesRefsTable(
    _$AppDatabase db,
  ) => MultiTypedResultKey.fromTable(
    db.invoices,
    aliasName: 'companies__id__invoices__company_id',
  );

  $$InvoicesTableProcessedTableManager get invoicesRefs {
    final manager = $$InvoicesTableTableManager(
      $_db,
      $_db.invoices,
    ).filter((f) => f.companyId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_invoicesRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }
}

class $$CompaniesTableFilterComposer
    extends Composer<_$AppDatabase, $CompaniesTable> {
  $$CompaniesTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get name => $composableBuilder(
    column: $table.name,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get contactPerson => $composableBuilder(
    column: $table.contactPerson,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get mobile => $composableBuilder(
    column: $table.mobile,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get email => $composableBuilder(
    column: $table.email,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get address => $composableBuilder(
    column: $table.address,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get gstin => $composableBuilder(
    column: $table.gstin,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<bool> get isActive => $composableBuilder(
    column: $table.isActive,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnFilters(column),
  );

  $$BranchesTableFilterComposer get branchId {
    final $$BranchesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.branchId,
      referencedTable: $db.branches,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$BranchesTableFilterComposer(
            $db: $db,
            $table: $db.branches,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  Expression<bool> sitesRefs(
    Expression<bool> Function($$SitesTableFilterComposer f) f,
  ) {
    final $$SitesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.sites,
      getReferencedColumn: (t) => t.companyId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SitesTableFilterComposer(
            $db: $db,
            $table: $db.sites,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<bool> contractsRefs(
    Expression<bool> Function($$ContractsTableFilterComposer f) f,
  ) {
    final $$ContractsTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.contracts,
      getReferencedColumn: (t) => t.companyId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$ContractsTableFilterComposer(
            $db: $db,
            $table: $db.contracts,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<bool> invoicesRefs(
    Expression<bool> Function($$InvoicesTableFilterComposer f) f,
  ) {
    final $$InvoicesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.invoices,
      getReferencedColumn: (t) => t.companyId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoicesTableFilterComposer(
            $db: $db,
            $table: $db.invoices,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$CompaniesTableOrderingComposer
    extends Composer<_$AppDatabase, $CompaniesTable> {
  $$CompaniesTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get name => $composableBuilder(
    column: $table.name,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get contactPerson => $composableBuilder(
    column: $table.contactPerson,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get mobile => $composableBuilder(
    column: $table.mobile,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get email => $composableBuilder(
    column: $table.email,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get address => $composableBuilder(
    column: $table.address,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get gstin => $composableBuilder(
    column: $table.gstin,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<bool> get isActive => $composableBuilder(
    column: $table.isActive,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnOrderings(column),
  );

  $$BranchesTableOrderingComposer get branchId {
    final $$BranchesTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.branchId,
      referencedTable: $db.branches,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$BranchesTableOrderingComposer(
            $db: $db,
            $table: $db.branches,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$CompaniesTableAnnotationComposer
    extends Composer<_$AppDatabase, $CompaniesTable> {
  $$CompaniesTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get name =>
      $composableBuilder(column: $table.name, builder: (column) => column);

  GeneratedColumn<String> get contactPerson => $composableBuilder(
    column: $table.contactPerson,
    builder: (column) => column,
  );

  GeneratedColumn<String> get mobile =>
      $composableBuilder(column: $table.mobile, builder: (column) => column);

  GeneratedColumn<String> get email =>
      $composableBuilder(column: $table.email, builder: (column) => column);

  GeneratedColumn<String> get address =>
      $composableBuilder(column: $table.address, builder: (column) => column);

  GeneratedColumn<String> get gstin =>
      $composableBuilder(column: $table.gstin, builder: (column) => column);

  GeneratedColumn<bool> get isActive =>
      $composableBuilder(column: $table.isActive, builder: (column) => column);

  GeneratedColumn<DateTime> get createdAt =>
      $composableBuilder(column: $table.createdAt, builder: (column) => column);

  $$BranchesTableAnnotationComposer get branchId {
    final $$BranchesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.branchId,
      referencedTable: $db.branches,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$BranchesTableAnnotationComposer(
            $db: $db,
            $table: $db.branches,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  Expression<T> sitesRefs<T extends Object>(
    Expression<T> Function($$SitesTableAnnotationComposer a) f,
  ) {
    final $$SitesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.sites,
      getReferencedColumn: (t) => t.companyId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SitesTableAnnotationComposer(
            $db: $db,
            $table: $db.sites,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<T> contractsRefs<T extends Object>(
    Expression<T> Function($$ContractsTableAnnotationComposer a) f,
  ) {
    final $$ContractsTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.contracts,
      getReferencedColumn: (t) => t.companyId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$ContractsTableAnnotationComposer(
            $db: $db,
            $table: $db.contracts,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<T> invoicesRefs<T extends Object>(
    Expression<T> Function($$InvoicesTableAnnotationComposer a) f,
  ) {
    final $$InvoicesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.invoices,
      getReferencedColumn: (t) => t.companyId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoicesTableAnnotationComposer(
            $db: $db,
            $table: $db.invoices,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$CompaniesTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $CompaniesTable,
          Company,
          $$CompaniesTableFilterComposer,
          $$CompaniesTableOrderingComposer,
          $$CompaniesTableAnnotationComposer,
          $$CompaniesTableCreateCompanionBuilder,
          $$CompaniesTableUpdateCompanionBuilder,
          (Company, $$CompaniesTableReferences),
          Company,
          PrefetchHooks Function({
            bool branchId,
            bool sitesRefs,
            bool contractsRefs,
            bool invoicesRefs,
          })
        > {
  $$CompaniesTableTableManager(_$AppDatabase db, $CompaniesTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$CompaniesTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$CompaniesTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$CompaniesTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> branchId = const Value.absent(),
                Value<String> name = const Value.absent(),
                Value<String> contactPerson = const Value.absent(),
                Value<String> mobile = const Value.absent(),
                Value<String> email = const Value.absent(),
                Value<String> address = const Value.absent(),
                Value<String> gstin = const Value.absent(),
                Value<bool> isActive = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => CompaniesCompanion(
                id: id,
                branchId: branchId,
                name: name,
                contactPerson: contactPerson,
                mobile: mobile,
                email: email,
                address: address,
                gstin: gstin,
                isActive: isActive,
                createdAt: createdAt,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String branchId,
                required String name,
                Value<String> contactPerson = const Value.absent(),
                Value<String> mobile = const Value.absent(),
                Value<String> email = const Value.absent(),
                Value<String> address = const Value.absent(),
                Value<String> gstin = const Value.absent(),
                Value<bool> isActive = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => CompaniesCompanion.insert(
                id: id,
                branchId: branchId,
                name: name,
                contactPerson: contactPerson,
                mobile: mobile,
                email: email,
                address: address,
                gstin: gstin,
                isActive: isActive,
                createdAt: createdAt,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$CompaniesTable, Company>(table),
                  $$CompaniesTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback:
              ({
                branchId = false,
                sitesRefs = false,
                contractsRefs = false,
                invoicesRefs = false,
              }) {
                return PrefetchHooks(
                  db: db,
                  explicitlyWatchedTables: [
                    if (sitesRefs) db.sites,
                    if (contractsRefs) db.contracts,
                    if (invoicesRefs) db.invoices,
                  ],
                  addJoins:
                      <
                        T extends TableManagerState<
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic
                        >
                      >(state) {
                        if (branchId) {
                          state = state.withJoin(
                            currentTable: table,
                            currentColumn: table.branchId,
                            referencedTable: $$CompaniesTableReferences
                                ._branchIdTable(db),
                            referencedColumn: $$CompaniesTableReferences
                                ._branchIdTable(db)
                                .id,
                          ) as T;
                        }

                        return state;
                      },
                  getPrefetchedDataCallback: (items) async {
                    return [
                      if (sitesRefs)
                        await $_getPrefetchedData<
                          Company,
                          $CompaniesTable,
                          Site
                        >(
                          currentTable: table,
                          referencedTable: $$CompaniesTableReferences
                              ._sitesRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$CompaniesTableReferences(
                                db,
                                table,
                                p0,
                              ).sitesRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.companyId == item.id,
                              ),
                          typedResults: items,
                        ),
                      if (contractsRefs)
                        await $_getPrefetchedData<
                          Company,
                          $CompaniesTable,
                          Contract
                        >(
                          currentTable: table,
                          referencedTable: $$CompaniesTableReferences
                              ._contractsRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$CompaniesTableReferences(
                                db,
                                table,
                                p0,
                              ).contractsRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.companyId == item.id,
                              ),
                          typedResults: items,
                        ),
                      if (invoicesRefs)
                        await $_getPrefetchedData<
                          Company,
                          $CompaniesTable,
                          Invoice
                        >(
                          currentTable: table,
                          referencedTable: $$CompaniesTableReferences
                              ._invoicesRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$CompaniesTableReferences(
                                db,
                                table,
                                p0,
                              ).invoicesRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.companyId == item.id,
                              ),
                          typedResults: items,
                        ),
                    ];
                  },
                );
              },
        ),
      );
}

typedef $$CompaniesTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $CompaniesTable,
      Company,
      $$CompaniesTableFilterComposer,
      $$CompaniesTableOrderingComposer,
      $$CompaniesTableAnnotationComposer,
      $$CompaniesTableCreateCompanionBuilder,
      $$CompaniesTableUpdateCompanionBuilder,
      (Company, $$CompaniesTableReferences),
      Company,
      PrefetchHooks Function({
        bool branchId,
        bool sitesRefs,
        bool contractsRefs,
        bool invoicesRefs,
      })
    >;
typedef $$SitesTableCreateCompanionBuilder = SitesCompanion Function({
  required String id,
  required String companyId,
  required String name,
  Value<String> address,
  Value<bool> isActive,
  Value<int> rowid,
});
typedef $$SitesTableUpdateCompanionBuilder = SitesCompanion Function({
  Value<String> id,
  Value<String> companyId,
  Value<String> name,
  Value<String> address,
  Value<bool> isActive,
  Value<int> rowid,
});

final class $$SitesTableReferences
    extends BaseReferences<_$AppDatabase, $SitesTable, Site> {
  $$SitesTableReferences(super.$_db, super.$_table, super.$_typedResult);

  static $CompaniesTable _companyIdTable(_$AppDatabase db) =>
      db.companies.createAlias('sites__company_id__companies__id');

  $$CompaniesTableProcessedTableManager get companyId {
    final $_column = $_itemColumn<String>('company_id')!;

    final manager = $$CompaniesTableTableManager(
      $_db,
      $_db.companies,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_companyIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }

  static MultiTypedResultKey<$AssignmentsTable, List<Assignment>>
  _assignmentsRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.assignments,
    aliasName: 'sites__id__assignments__site_id',
  );

  $$AssignmentsTableProcessedTableManager get assignmentsRefs {
    final manager = $$AssignmentsTableTableManager(
      $_db,
      $_db.assignments,
    ).filter((f) => f.siteId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_assignmentsRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }

  static MultiTypedResultKey<$AttendanceTable, List<AttendanceData>>
  _attendanceRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.attendance,
    aliasName: 'sites__id__attendance__site_id',
  );

  $$AttendanceTableProcessedTableManager get attendanceRefs {
    final manager = $$AttendanceTableTableManager(
      $_db,
      $_db.attendance,
    ).filter((f) => f.siteId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_attendanceRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }

  static MultiTypedResultKey<$AttendanceSheetsTable, List<AttendanceSheet>>
  _attendanceSheetsRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.attendanceSheets,
    aliasName: 'sites__id__attendance_sheets__site_id',
  );

  $$AttendanceSheetsTableProcessedTableManager get attendanceSheetsRefs {
    final manager = $$AttendanceSheetsTableTableManager(
      $_db,
      $_db.attendanceSheets,
    ).filter((f) => f.siteId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(
      _attendanceSheetsRefsTable($_db),
    );
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }

  static MultiTypedResultKey<$InvoicesTable, List<Invoice>> _invoicesRefsTable(
    _$AppDatabase db,
  ) => MultiTypedResultKey.fromTable(
    db.invoices,
    aliasName: 'sites__id__invoices__site_id',
  );

  $$InvoicesTableProcessedTableManager get invoicesRefs {
    final manager = $$InvoicesTableTableManager(
      $_db,
      $_db.invoices,
    ).filter((f) => f.siteId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_invoicesRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }
}

class $$SitesTableFilterComposer extends Composer<_$AppDatabase, $SitesTable> {
  $$SitesTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get name => $composableBuilder(
    column: $table.name,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get address => $composableBuilder(
    column: $table.address,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<bool> get isActive => $composableBuilder(
    column: $table.isActive,
    builder: (column) => ColumnFilters(column),
  );

  $$CompaniesTableFilterComposer get companyId {
    final $$CompaniesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.companyId,
      referencedTable: $db.companies,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$CompaniesTableFilterComposer(
            $db: $db,
            $table: $db.companies,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  Expression<bool> assignmentsRefs(
    Expression<bool> Function($$AssignmentsTableFilterComposer f) f,
  ) {
    final $$AssignmentsTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.assignments,
      getReferencedColumn: (t) => t.siteId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$AssignmentsTableFilterComposer(
            $db: $db,
            $table: $db.assignments,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<bool> attendanceRefs(
    Expression<bool> Function($$AttendanceTableFilterComposer f) f,
  ) {
    final $$AttendanceTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.attendance,
      getReferencedColumn: (t) => t.siteId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$AttendanceTableFilterComposer(
            $db: $db,
            $table: $db.attendance,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<bool> attendanceSheetsRefs(
    Expression<bool> Function($$AttendanceSheetsTableFilterComposer f) f,
  ) {
    final $$AttendanceSheetsTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.attendanceSheets,
      getReferencedColumn: (t) => t.siteId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$AttendanceSheetsTableFilterComposer(
            $db: $db,
            $table: $db.attendanceSheets,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<bool> invoicesRefs(
    Expression<bool> Function($$InvoicesTableFilterComposer f) f,
  ) {
    final $$InvoicesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.invoices,
      getReferencedColumn: (t) => t.siteId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoicesTableFilterComposer(
            $db: $db,
            $table: $db.invoices,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$SitesTableOrderingComposer
    extends Composer<_$AppDatabase, $SitesTable> {
  $$SitesTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get name => $composableBuilder(
    column: $table.name,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get address => $composableBuilder(
    column: $table.address,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<bool> get isActive => $composableBuilder(
    column: $table.isActive,
    builder: (column) => ColumnOrderings(column),
  );

  $$CompaniesTableOrderingComposer get companyId {
    final $$CompaniesTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.companyId,
      referencedTable: $db.companies,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$CompaniesTableOrderingComposer(
            $db: $db,
            $table: $db.companies,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$SitesTableAnnotationComposer
    extends Composer<_$AppDatabase, $SitesTable> {
  $$SitesTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get name =>
      $composableBuilder(column: $table.name, builder: (column) => column);

  GeneratedColumn<String> get address =>
      $composableBuilder(column: $table.address, builder: (column) => column);

  GeneratedColumn<bool> get isActive =>
      $composableBuilder(column: $table.isActive, builder: (column) => column);

  $$CompaniesTableAnnotationComposer get companyId {
    final $$CompaniesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.companyId,
      referencedTable: $db.companies,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$CompaniesTableAnnotationComposer(
            $db: $db,
            $table: $db.companies,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  Expression<T> assignmentsRefs<T extends Object>(
    Expression<T> Function($$AssignmentsTableAnnotationComposer a) f,
  ) {
    final $$AssignmentsTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.assignments,
      getReferencedColumn: (t) => t.siteId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$AssignmentsTableAnnotationComposer(
            $db: $db,
            $table: $db.assignments,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<T> attendanceRefs<T extends Object>(
    Expression<T> Function($$AttendanceTableAnnotationComposer a) f,
  ) {
    final $$AttendanceTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.attendance,
      getReferencedColumn: (t) => t.siteId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$AttendanceTableAnnotationComposer(
            $db: $db,
            $table: $db.attendance,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<T> attendanceSheetsRefs<T extends Object>(
    Expression<T> Function($$AttendanceSheetsTableAnnotationComposer a) f,
  ) {
    final $$AttendanceSheetsTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.attendanceSheets,
      getReferencedColumn: (t) => t.siteId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$AttendanceSheetsTableAnnotationComposer(
            $db: $db,
            $table: $db.attendanceSheets,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<T> invoicesRefs<T extends Object>(
    Expression<T> Function($$InvoicesTableAnnotationComposer a) f,
  ) {
    final $$InvoicesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.invoices,
      getReferencedColumn: (t) => t.siteId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoicesTableAnnotationComposer(
            $db: $db,
            $table: $db.invoices,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$SitesTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $SitesTable,
          Site,
          $$SitesTableFilterComposer,
          $$SitesTableOrderingComposer,
          $$SitesTableAnnotationComposer,
          $$SitesTableCreateCompanionBuilder,
          $$SitesTableUpdateCompanionBuilder,
          (Site, $$SitesTableReferences),
          Site,
          PrefetchHooks Function({
            bool companyId,
            bool assignmentsRefs,
            bool attendanceRefs,
            bool attendanceSheetsRefs,
            bool invoicesRefs,
          })
        > {
  $$SitesTableTableManager(_$AppDatabase db, $SitesTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$SitesTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$SitesTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$SitesTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> companyId = const Value.absent(),
                Value<String> name = const Value.absent(),
                Value<String> address = const Value.absent(),
                Value<bool> isActive = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => SitesCompanion(
                id: id,
                companyId: companyId,
                name: name,
                address: address,
                isActive: isActive,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String companyId,
                required String name,
                Value<String> address = const Value.absent(),
                Value<bool> isActive = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => SitesCompanion.insert(
                id: id,
                companyId: companyId,
                name: name,
                address: address,
                isActive: isActive,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$SitesTable, Site>(table),
                  $$SitesTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback:
              ({
                companyId = false,
                assignmentsRefs = false,
                attendanceRefs = false,
                attendanceSheetsRefs = false,
                invoicesRefs = false,
              }) {
                return PrefetchHooks(
                  db: db,
                  explicitlyWatchedTables: [
                    if (assignmentsRefs) db.assignments,
                    if (attendanceRefs) db.attendance,
                    if (attendanceSheetsRefs) db.attendanceSheets,
                    if (invoicesRefs) db.invoices,
                  ],
                  addJoins:
                      <
                        T extends TableManagerState<
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic
                        >
                      >(state) {
                        if (companyId) {
                          state = state.withJoin(
                            currentTable: table,
                            currentColumn: table.companyId,
                            referencedTable: $$SitesTableReferences
                                ._companyIdTable(db),
                            referencedColumn: $$SitesTableReferences
                                ._companyIdTable(db)
                                .id,
                          ) as T;
                        }

                        return state;
                      },
                  getPrefetchedDataCallback: (items) async {
                    return [
                      if (assignmentsRefs)
                        await $_getPrefetchedData<
                          Site,
                          $SitesTable,
                          Assignment
                        >(
                          currentTable: table,
                          referencedTable: $$SitesTableReferences
                              ._assignmentsRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$SitesTableReferences(
                                db,
                                table,
                                p0,
                              ).assignmentsRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.siteId == item.id,
                              ),
                          typedResults: items,
                        ),
                      if (attendanceRefs)
                        await $_getPrefetchedData<
                          Site,
                          $SitesTable,
                          AttendanceData
                        >(
                          currentTable: table,
                          referencedTable: $$SitesTableReferences
                              ._attendanceRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$SitesTableReferences(
                                db,
                                table,
                                p0,
                              ).attendanceRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.siteId == item.id,
                              ),
                          typedResults: items,
                        ),
                      if (attendanceSheetsRefs)
                        await $_getPrefetchedData<
                          Site,
                          $SitesTable,
                          AttendanceSheet
                        >(
                          currentTable: table,
                          referencedTable: $$SitesTableReferences
                              ._attendanceSheetsRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$SitesTableReferences(
                                db,
                                table,
                                p0,
                              ).attendanceSheetsRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.siteId == item.id,
                              ),
                          typedResults: items,
                        ),
                      if (invoicesRefs)
                        await $_getPrefetchedData<Site, $SitesTable, Invoice>(
                          currentTable: table,
                          referencedTable: $$SitesTableReferences
                              ._invoicesRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$SitesTableReferences(
                                db,
                                table,
                                p0,
                              ).invoicesRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.siteId == item.id,
                              ),
                          typedResults: items,
                        ),
                    ];
                  },
                );
              },
        ),
      );
}

typedef $$SitesTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $SitesTable,
      Site,
      $$SitesTableFilterComposer,
      $$SitesTableOrderingComposer,
      $$SitesTableAnnotationComposer,
      $$SitesTableCreateCompanionBuilder,
      $$SitesTableUpdateCompanionBuilder,
      (Site, $$SitesTableReferences),
      Site,
      PrefetchHooks Function({
        bool companyId,
        bool assignmentsRefs,
        bool attendanceRefs,
        bool attendanceSheetsRefs,
        bool invoicesRefs,
      })
    >;
typedef $$ContractsTableCreateCompanionBuilder = ContractsCompanion Function({
  required String id,
  required String companyId,
  required String title,
  required String startDate,
  Value<String?> endDate,
  Value<int> paymentTermsDays,
  Value<String> notes,
  Value<String?> docPath,
  Value<bool> isActive,
  Value<int> rowid,
});
typedef $$ContractsTableUpdateCompanionBuilder = ContractsCompanion Function({
  Value<String> id,
  Value<String> companyId,
  Value<String> title,
  Value<String> startDate,
  Value<String?> endDate,
  Value<int> paymentTermsDays,
  Value<String> notes,
  Value<String?> docPath,
  Value<bool> isActive,
  Value<int> rowid,
});

final class $$ContractsTableReferences
    extends BaseReferences<_$AppDatabase, $ContractsTable, Contract> {
  $$ContractsTableReferences(super.$_db, super.$_table, super.$_typedResult);

  static $CompaniesTable _companyIdTable(_$AppDatabase db) =>
      db.companies.createAlias('contracts__company_id__companies__id');

  $$CompaniesTableProcessedTableManager get companyId {
    final $_column = $_itemColumn<String>('company_id')!;

    final manager = $$CompaniesTableTableManager(
      $_db,
      $_db.companies,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_companyIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }

  static MultiTypedResultKey<$ContractRatesTable, List<ContractRate>>
  _contractRatesRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.contractRates,
    aliasName: 'contracts__id__contract_rates__contract_id',
  );

  $$ContractRatesTableProcessedTableManager get contractRatesRefs {
    final manager = $$ContractRatesTableTableManager(
      $_db,
      $_db.contractRates,
    ).filter((f) => f.contractId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_contractRatesRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }
}

class $$ContractsTableFilterComposer
    extends Composer<_$AppDatabase, $ContractsTable> {
  $$ContractsTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get title => $composableBuilder(
    column: $table.title,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get startDate => $composableBuilder(
    column: $table.startDate,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get endDate => $composableBuilder(
    column: $table.endDate,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<int> get paymentTermsDays => $composableBuilder(
    column: $table.paymentTermsDays,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get notes => $composableBuilder(
    column: $table.notes,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get docPath => $composableBuilder(
    column: $table.docPath,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<bool> get isActive => $composableBuilder(
    column: $table.isActive,
    builder: (column) => ColumnFilters(column),
  );

  $$CompaniesTableFilterComposer get companyId {
    final $$CompaniesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.companyId,
      referencedTable: $db.companies,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$CompaniesTableFilterComposer(
            $db: $db,
            $table: $db.companies,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  Expression<bool> contractRatesRefs(
    Expression<bool> Function($$ContractRatesTableFilterComposer f) f,
  ) {
    final $$ContractRatesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.contractRates,
      getReferencedColumn: (t) => t.contractId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$ContractRatesTableFilterComposer(
            $db: $db,
            $table: $db.contractRates,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$ContractsTableOrderingComposer
    extends Composer<_$AppDatabase, $ContractsTable> {
  $$ContractsTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get title => $composableBuilder(
    column: $table.title,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get startDate => $composableBuilder(
    column: $table.startDate,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get endDate => $composableBuilder(
    column: $table.endDate,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<int> get paymentTermsDays => $composableBuilder(
    column: $table.paymentTermsDays,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get notes => $composableBuilder(
    column: $table.notes,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get docPath => $composableBuilder(
    column: $table.docPath,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<bool> get isActive => $composableBuilder(
    column: $table.isActive,
    builder: (column) => ColumnOrderings(column),
  );

  $$CompaniesTableOrderingComposer get companyId {
    final $$CompaniesTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.companyId,
      referencedTable: $db.companies,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$CompaniesTableOrderingComposer(
            $db: $db,
            $table: $db.companies,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$ContractsTableAnnotationComposer
    extends Composer<_$AppDatabase, $ContractsTable> {
  $$ContractsTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get title =>
      $composableBuilder(column: $table.title, builder: (column) => column);

  GeneratedColumn<String> get startDate =>
      $composableBuilder(column: $table.startDate, builder: (column) => column);

  GeneratedColumn<String> get endDate =>
      $composableBuilder(column: $table.endDate, builder: (column) => column);

  GeneratedColumn<int> get paymentTermsDays => $composableBuilder(
    column: $table.paymentTermsDays,
    builder: (column) => column,
  );

  GeneratedColumn<String> get notes =>
      $composableBuilder(column: $table.notes, builder: (column) => column);

  GeneratedColumn<String> get docPath =>
      $composableBuilder(column: $table.docPath, builder: (column) => column);

  GeneratedColumn<bool> get isActive =>
      $composableBuilder(column: $table.isActive, builder: (column) => column);

  $$CompaniesTableAnnotationComposer get companyId {
    final $$CompaniesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.companyId,
      referencedTable: $db.companies,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$CompaniesTableAnnotationComposer(
            $db: $db,
            $table: $db.companies,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  Expression<T> contractRatesRefs<T extends Object>(
    Expression<T> Function($$ContractRatesTableAnnotationComposer a) f,
  ) {
    final $$ContractRatesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.contractRates,
      getReferencedColumn: (t) => t.contractId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$ContractRatesTableAnnotationComposer(
            $db: $db,
            $table: $db.contractRates,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$ContractsTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $ContractsTable,
          Contract,
          $$ContractsTableFilterComposer,
          $$ContractsTableOrderingComposer,
          $$ContractsTableAnnotationComposer,
          $$ContractsTableCreateCompanionBuilder,
          $$ContractsTableUpdateCompanionBuilder,
          (Contract, $$ContractsTableReferences),
          Contract,
          PrefetchHooks Function({bool companyId, bool contractRatesRefs})
        > {
  $$ContractsTableTableManager(_$AppDatabase db, $ContractsTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$ContractsTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$ContractsTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$ContractsTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> companyId = const Value.absent(),
                Value<String> title = const Value.absent(),
                Value<String> startDate = const Value.absent(),
                Value<String?> endDate = const Value.absent(),
                Value<int> paymentTermsDays = const Value.absent(),
                Value<String> notes = const Value.absent(),
                Value<String?> docPath = const Value.absent(),
                Value<bool> isActive = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => ContractsCompanion(
                id: id,
                companyId: companyId,
                title: title,
                startDate: startDate,
                endDate: endDate,
                paymentTermsDays: paymentTermsDays,
                notes: notes,
                docPath: docPath,
                isActive: isActive,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String companyId,
                required String title,
                required String startDate,
                Value<String?> endDate = const Value.absent(),
                Value<int> paymentTermsDays = const Value.absent(),
                Value<String> notes = const Value.absent(),
                Value<String?> docPath = const Value.absent(),
                Value<bool> isActive = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => ContractsCompanion.insert(
                id: id,
                companyId: companyId,
                title: title,
                startDate: startDate,
                endDate: endDate,
                paymentTermsDays: paymentTermsDays,
                notes: notes,
                docPath: docPath,
                isActive: isActive,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$ContractsTable, Contract>(table),
                  $$ContractsTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback:
              ({companyId = false, contractRatesRefs = false}) {
                return PrefetchHooks(
                  db: db,
                  explicitlyWatchedTables: [
                    if (contractRatesRefs) db.contractRates,
                  ],
                  addJoins:
                      <
                        T extends TableManagerState<
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic
                        >
                      >(state) {
                        if (companyId) {
                          state = state.withJoin(
                            currentTable: table,
                            currentColumn: table.companyId,
                            referencedTable: $$ContractsTableReferences
                                ._companyIdTable(db),
                            referencedColumn: $$ContractsTableReferences
                                ._companyIdTable(db)
                                .id,
                          ) as T;
                        }

                        return state;
                      },
                  getPrefetchedDataCallback: (items) async {
                    return [
                      if (contractRatesRefs)
                        await $_getPrefetchedData<
                          Contract,
                          $ContractsTable,
                          ContractRate
                        >(
                          currentTable: table,
                          referencedTable: $$ContractsTableReferences
                              ._contractRatesRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$ContractsTableReferences(
                                db,
                                table,
                                p0,
                              ).contractRatesRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.contractId == item.id,
                              ),
                          typedResults: items,
                        ),
                    ];
                  },
                );
              },
        ),
      );
}

typedef $$ContractsTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $ContractsTable,
      Contract,
      $$ContractsTableFilterComposer,
      $$ContractsTableOrderingComposer,
      $$ContractsTableAnnotationComposer,
      $$ContractsTableCreateCompanionBuilder,
      $$ContractsTableUpdateCompanionBuilder,
      (Contract, $$ContractsTableReferences),
      Contract,
      PrefetchHooks Function({bool companyId, bool contractRatesRefs})
    >;
typedef $$ContractRatesTableCreateCompanionBuilder =
    ContractRatesCompanion Function({
      required String id,
      required String contractId,
      required String skill,
      required int perDay,
      Value<int> otPerHour,
      Value<int> rowid,
    });
typedef $$ContractRatesTableUpdateCompanionBuilder =
    ContractRatesCompanion Function({
      Value<String> id,
      Value<String> contractId,
      Value<String> skill,
      Value<int> perDay,
      Value<int> otPerHour,
      Value<int> rowid,
    });

final class $$ContractRatesTableReferences
    extends BaseReferences<_$AppDatabase, $ContractRatesTable, ContractRate> {
  $$ContractRatesTableReferences(
    super.$_db,
    super.$_table,
    super.$_typedResult,
  );

  static $ContractsTable _contractIdTable(_$AppDatabase db) =>
      db.contracts.createAlias('contract_rates__contract_id__contracts__id');

  $$ContractsTableProcessedTableManager get contractId {
    final $_column = $_itemColumn<String>('contract_id')!;

    final manager = $$ContractsTableTableManager(
      $_db,
      $_db.contracts,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_contractIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }
}

class $$ContractRatesTableFilterComposer
    extends Composer<_$AppDatabase, $ContractRatesTable> {
  $$ContractRatesTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get skill => $composableBuilder(
    column: $table.skill,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<int> get perDay => $composableBuilder(
    column: $table.perDay,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<int> get otPerHour => $composableBuilder(
    column: $table.otPerHour,
    builder: (column) => ColumnFilters(column),
  );

  $$ContractsTableFilterComposer get contractId {
    final $$ContractsTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.contractId,
      referencedTable: $db.contracts,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$ContractsTableFilterComposer(
            $db: $db,
            $table: $db.contracts,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$ContractRatesTableOrderingComposer
    extends Composer<_$AppDatabase, $ContractRatesTable> {
  $$ContractRatesTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get skill => $composableBuilder(
    column: $table.skill,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<int> get perDay => $composableBuilder(
    column: $table.perDay,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<int> get otPerHour => $composableBuilder(
    column: $table.otPerHour,
    builder: (column) => ColumnOrderings(column),
  );

  $$ContractsTableOrderingComposer get contractId {
    final $$ContractsTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.contractId,
      referencedTable: $db.contracts,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$ContractsTableOrderingComposer(
            $db: $db,
            $table: $db.contracts,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$ContractRatesTableAnnotationComposer
    extends Composer<_$AppDatabase, $ContractRatesTable> {
  $$ContractRatesTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get skill =>
      $composableBuilder(column: $table.skill, builder: (column) => column);

  GeneratedColumn<int> get perDay =>
      $composableBuilder(column: $table.perDay, builder: (column) => column);

  GeneratedColumn<int> get otPerHour =>
      $composableBuilder(column: $table.otPerHour, builder: (column) => column);

  $$ContractsTableAnnotationComposer get contractId {
    final $$ContractsTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.contractId,
      referencedTable: $db.contracts,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$ContractsTableAnnotationComposer(
            $db: $db,
            $table: $db.contracts,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$ContractRatesTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $ContractRatesTable,
          ContractRate,
          $$ContractRatesTableFilterComposer,
          $$ContractRatesTableOrderingComposer,
          $$ContractRatesTableAnnotationComposer,
          $$ContractRatesTableCreateCompanionBuilder,
          $$ContractRatesTableUpdateCompanionBuilder,
          (ContractRate, $$ContractRatesTableReferences),
          ContractRate,
          PrefetchHooks Function({bool contractId})
        > {
  $$ContractRatesTableTableManager(_$AppDatabase db, $ContractRatesTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$ContractRatesTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$ContractRatesTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$ContractRatesTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> contractId = const Value.absent(),
                Value<String> skill = const Value.absent(),
                Value<int> perDay = const Value.absent(),
                Value<int> otPerHour = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => ContractRatesCompanion(
                id: id,
                contractId: contractId,
                skill: skill,
                perDay: perDay,
                otPerHour: otPerHour,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String contractId,
                required String skill,
                required int perDay,
                Value<int> otPerHour = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => ContractRatesCompanion.insert(
                id: id,
                contractId: contractId,
                skill: skill,
                perDay: perDay,
                otPerHour: otPerHour,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$ContractRatesTable, ContractRate>(table),
                  $$ContractRatesTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback: ({contractId = false}) {
            return PrefetchHooks(
              db: db,
              explicitlyWatchedTables: [],
              addJoins:
                  <
                    T extends TableManagerState<
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic
                    >
                  >(state) {
                    if (contractId) {
                      state = state.withJoin(
                        currentTable: table,
                        currentColumn: table.contractId,
                        referencedTable: $$ContractRatesTableReferences
                            ._contractIdTable(db),
                        referencedColumn: $$ContractRatesTableReferences
                            ._contractIdTable(db)
                            .id,
                      ) as T;
                    }

                    return state;
                  },
              getPrefetchedDataCallback: (items) async {
                return [];
              },
            );
          },
        ),
      );
}

typedef $$ContractRatesTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $ContractRatesTable,
      ContractRate,
      $$ContractRatesTableFilterComposer,
      $$ContractRatesTableOrderingComposer,
      $$ContractRatesTableAnnotationComposer,
      $$ContractRatesTableCreateCompanionBuilder,
      $$ContractRatesTableUpdateCompanionBuilder,
      (ContractRate, $$ContractRatesTableReferences),
      ContractRate,
      PrefetchHooks Function({bool contractId})
    >;
typedef $$LaboursTableCreateCompanionBuilder = LaboursCompanion Function({
  required String id,
  required String branchId,
  required String name,
  Value<String> fatherName,
  Value<String> mobile,
  Value<String> address,
  Value<String> skill,
  Value<String?> photoPath,
  required String joinDate,
  Value<String?> leaveDate,
  Value<String> status,
  Value<String> note,
  Value<DateTime> createdAt,
  Value<int> rowid,
});
typedef $$LaboursTableUpdateCompanionBuilder = LaboursCompanion Function({
  Value<String> id,
  Value<String> branchId,
  Value<String> name,
  Value<String> fatherName,
  Value<String> mobile,
  Value<String> address,
  Value<String> skill,
  Value<String?> photoPath,
  Value<String> joinDate,
  Value<String?> leaveDate,
  Value<String> status,
  Value<String> note,
  Value<DateTime> createdAt,
  Value<int> rowid,
});

final class $$LaboursTableReferences
    extends BaseReferences<_$AppDatabase, $LaboursTable, Labour> {
  $$LaboursTableReferences(super.$_db, super.$_table, super.$_typedResult);

  static $BranchesTable _branchIdTable(_$AppDatabase db) =>
      db.branches.createAlias('labours__branch_id__branches__id');

  $$BranchesTableProcessedTableManager get branchId {
    final $_column = $_itemColumn<String>('branch_id')!;

    final manager = $$BranchesTableTableManager(
      $_db,
      $_db.branches,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_branchIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }

  static MultiTypedResultKey<$LabourRatesTable, List<LabourRate>>
  _labourRatesRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.labourRates,
    aliasName: 'labours__id__labour_rates__labour_id',
  );

  $$LabourRatesTableProcessedTableManager get labourRatesRefs {
    final manager = $$LabourRatesTableTableManager(
      $_db,
      $_db.labourRates,
    ).filter((f) => f.labourId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_labourRatesRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }

  static MultiTypedResultKey<$LabourDocumentsTable, List<LabourDocument>>
  _labourDocumentsRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.labourDocuments,
    aliasName: 'labours__id__labour_documents__labour_id',
  );

  $$LabourDocumentsTableProcessedTableManager get labourDocumentsRefs {
    final manager = $$LabourDocumentsTableTableManager(
      $_db,
      $_db.labourDocuments,
    ).filter((f) => f.labourId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(
      _labourDocumentsRefsTable($_db),
    );
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }

  static MultiTypedResultKey<$AssignmentsTable, List<Assignment>>
  _assignmentsRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.assignments,
    aliasName: 'labours__id__assignments__labour_id',
  );

  $$AssignmentsTableProcessedTableManager get assignmentsRefs {
    final manager = $$AssignmentsTableTableManager(
      $_db,
      $_db.assignments,
    ).filter((f) => f.labourId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_assignmentsRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }

  static MultiTypedResultKey<$AttendanceTable, List<AttendanceData>>
  _attendanceRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.attendance,
    aliasName: 'labours__id__attendance__labour_id',
  );

  $$AttendanceTableProcessedTableManager get attendanceRefs {
    final manager = $$AttendanceTableTableManager(
      $_db,
      $_db.attendance,
    ).filter((f) => f.labourId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_attendanceRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }

  static MultiTypedResultKey<$PaymentsTable, List<Payment>> _paymentsRefsTable(
    _$AppDatabase db,
  ) => MultiTypedResultKey.fromTable(
    db.payments,
    aliasName: 'labours__id__payments__labour_id',
  );

  $$PaymentsTableProcessedTableManager get paymentsRefs {
    final manager = $$PaymentsTableTableManager(
      $_db,
      $_db.payments,
    ).filter((f) => f.labourId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_paymentsRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }

  static MultiTypedResultKey<$InvoiceLinesTable, List<InvoiceLine>>
  _invoiceLinesRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.invoiceLines,
    aliasName: 'labours__id__invoice_lines__labour_id',
  );

  $$InvoiceLinesTableProcessedTableManager get invoiceLinesRefs {
    final manager = $$InvoiceLinesTableTableManager(
      $_db,
      $_db.invoiceLines,
    ).filter((f) => f.labourId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_invoiceLinesRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }
}

class $$LaboursTableFilterComposer
    extends Composer<_$AppDatabase, $LaboursTable> {
  $$LaboursTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get name => $composableBuilder(
    column: $table.name,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get fatherName => $composableBuilder(
    column: $table.fatherName,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get mobile => $composableBuilder(
    column: $table.mobile,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get address => $composableBuilder(
    column: $table.address,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get skill => $composableBuilder(
    column: $table.skill,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get photoPath => $composableBuilder(
    column: $table.photoPath,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get joinDate => $composableBuilder(
    column: $table.joinDate,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get leaveDate => $composableBuilder(
    column: $table.leaveDate,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get status => $composableBuilder(
    column: $table.status,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get note => $composableBuilder(
    column: $table.note,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnFilters(column),
  );

  $$BranchesTableFilterComposer get branchId {
    final $$BranchesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.branchId,
      referencedTable: $db.branches,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$BranchesTableFilterComposer(
            $db: $db,
            $table: $db.branches,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  Expression<bool> labourRatesRefs(
    Expression<bool> Function($$LabourRatesTableFilterComposer f) f,
  ) {
    final $$LabourRatesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.labourRates,
      getReferencedColumn: (t) => t.labourId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LabourRatesTableFilterComposer(
            $db: $db,
            $table: $db.labourRates,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<bool> labourDocumentsRefs(
    Expression<bool> Function($$LabourDocumentsTableFilterComposer f) f,
  ) {
    final $$LabourDocumentsTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.labourDocuments,
      getReferencedColumn: (t) => t.labourId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LabourDocumentsTableFilterComposer(
            $db: $db,
            $table: $db.labourDocuments,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<bool> assignmentsRefs(
    Expression<bool> Function($$AssignmentsTableFilterComposer f) f,
  ) {
    final $$AssignmentsTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.assignments,
      getReferencedColumn: (t) => t.labourId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$AssignmentsTableFilterComposer(
            $db: $db,
            $table: $db.assignments,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<bool> attendanceRefs(
    Expression<bool> Function($$AttendanceTableFilterComposer f) f,
  ) {
    final $$AttendanceTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.attendance,
      getReferencedColumn: (t) => t.labourId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$AttendanceTableFilterComposer(
            $db: $db,
            $table: $db.attendance,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<bool> paymentsRefs(
    Expression<bool> Function($$PaymentsTableFilterComposer f) f,
  ) {
    final $$PaymentsTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.payments,
      getReferencedColumn: (t) => t.labourId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$PaymentsTableFilterComposer(
            $db: $db,
            $table: $db.payments,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<bool> invoiceLinesRefs(
    Expression<bool> Function($$InvoiceLinesTableFilterComposer f) f,
  ) {
    final $$InvoiceLinesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.invoiceLines,
      getReferencedColumn: (t) => t.labourId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoiceLinesTableFilterComposer(
            $db: $db,
            $table: $db.invoiceLines,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$LaboursTableOrderingComposer
    extends Composer<_$AppDatabase, $LaboursTable> {
  $$LaboursTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get name => $composableBuilder(
    column: $table.name,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get fatherName => $composableBuilder(
    column: $table.fatherName,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get mobile => $composableBuilder(
    column: $table.mobile,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get address => $composableBuilder(
    column: $table.address,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get skill => $composableBuilder(
    column: $table.skill,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get photoPath => $composableBuilder(
    column: $table.photoPath,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get joinDate => $composableBuilder(
    column: $table.joinDate,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get leaveDate => $composableBuilder(
    column: $table.leaveDate,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get status => $composableBuilder(
    column: $table.status,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get note => $composableBuilder(
    column: $table.note,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnOrderings(column),
  );

  $$BranchesTableOrderingComposer get branchId {
    final $$BranchesTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.branchId,
      referencedTable: $db.branches,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$BranchesTableOrderingComposer(
            $db: $db,
            $table: $db.branches,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$LaboursTableAnnotationComposer
    extends Composer<_$AppDatabase, $LaboursTable> {
  $$LaboursTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get name =>
      $composableBuilder(column: $table.name, builder: (column) => column);

  GeneratedColumn<String> get fatherName => $composableBuilder(
    column: $table.fatherName,
    builder: (column) => column,
  );

  GeneratedColumn<String> get mobile =>
      $composableBuilder(column: $table.mobile, builder: (column) => column);

  GeneratedColumn<String> get address =>
      $composableBuilder(column: $table.address, builder: (column) => column);

  GeneratedColumn<String> get skill =>
      $composableBuilder(column: $table.skill, builder: (column) => column);

  GeneratedColumn<String> get photoPath =>
      $composableBuilder(column: $table.photoPath, builder: (column) => column);

  GeneratedColumn<String> get joinDate =>
      $composableBuilder(column: $table.joinDate, builder: (column) => column);

  GeneratedColumn<String> get leaveDate =>
      $composableBuilder(column: $table.leaveDate, builder: (column) => column);

  GeneratedColumn<String> get status =>
      $composableBuilder(column: $table.status, builder: (column) => column);

  GeneratedColumn<String> get note =>
      $composableBuilder(column: $table.note, builder: (column) => column);

  GeneratedColumn<DateTime> get createdAt =>
      $composableBuilder(column: $table.createdAt, builder: (column) => column);

  $$BranchesTableAnnotationComposer get branchId {
    final $$BranchesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.branchId,
      referencedTable: $db.branches,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$BranchesTableAnnotationComposer(
            $db: $db,
            $table: $db.branches,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  Expression<T> labourRatesRefs<T extends Object>(
    Expression<T> Function($$LabourRatesTableAnnotationComposer a) f,
  ) {
    final $$LabourRatesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.labourRates,
      getReferencedColumn: (t) => t.labourId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LabourRatesTableAnnotationComposer(
            $db: $db,
            $table: $db.labourRates,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<T> labourDocumentsRefs<T extends Object>(
    Expression<T> Function($$LabourDocumentsTableAnnotationComposer a) f,
  ) {
    final $$LabourDocumentsTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.labourDocuments,
      getReferencedColumn: (t) => t.labourId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LabourDocumentsTableAnnotationComposer(
            $db: $db,
            $table: $db.labourDocuments,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<T> assignmentsRefs<T extends Object>(
    Expression<T> Function($$AssignmentsTableAnnotationComposer a) f,
  ) {
    final $$AssignmentsTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.assignments,
      getReferencedColumn: (t) => t.labourId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$AssignmentsTableAnnotationComposer(
            $db: $db,
            $table: $db.assignments,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<T> attendanceRefs<T extends Object>(
    Expression<T> Function($$AttendanceTableAnnotationComposer a) f,
  ) {
    final $$AttendanceTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.attendance,
      getReferencedColumn: (t) => t.labourId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$AttendanceTableAnnotationComposer(
            $db: $db,
            $table: $db.attendance,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<T> paymentsRefs<T extends Object>(
    Expression<T> Function($$PaymentsTableAnnotationComposer a) f,
  ) {
    final $$PaymentsTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.payments,
      getReferencedColumn: (t) => t.labourId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$PaymentsTableAnnotationComposer(
            $db: $db,
            $table: $db.payments,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<T> invoiceLinesRefs<T extends Object>(
    Expression<T> Function($$InvoiceLinesTableAnnotationComposer a) f,
  ) {
    final $$InvoiceLinesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.invoiceLines,
      getReferencedColumn: (t) => t.labourId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoiceLinesTableAnnotationComposer(
            $db: $db,
            $table: $db.invoiceLines,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$LaboursTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $LaboursTable,
          Labour,
          $$LaboursTableFilterComposer,
          $$LaboursTableOrderingComposer,
          $$LaboursTableAnnotationComposer,
          $$LaboursTableCreateCompanionBuilder,
          $$LaboursTableUpdateCompanionBuilder,
          (Labour, $$LaboursTableReferences),
          Labour,
          PrefetchHooks Function({
            bool branchId,
            bool labourRatesRefs,
            bool labourDocumentsRefs,
            bool assignmentsRefs,
            bool attendanceRefs,
            bool paymentsRefs,
            bool invoiceLinesRefs,
          })
        > {
  $$LaboursTableTableManager(_$AppDatabase db, $LaboursTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$LaboursTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$LaboursTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$LaboursTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> branchId = const Value.absent(),
                Value<String> name = const Value.absent(),
                Value<String> fatherName = const Value.absent(),
                Value<String> mobile = const Value.absent(),
                Value<String> address = const Value.absent(),
                Value<String> skill = const Value.absent(),
                Value<String?> photoPath = const Value.absent(),
                Value<String> joinDate = const Value.absent(),
                Value<String?> leaveDate = const Value.absent(),
                Value<String> status = const Value.absent(),
                Value<String> note = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => LaboursCompanion(
                id: id,
                branchId: branchId,
                name: name,
                fatherName: fatherName,
                mobile: mobile,
                address: address,
                skill: skill,
                photoPath: photoPath,
                joinDate: joinDate,
                leaveDate: leaveDate,
                status: status,
                note: note,
                createdAt: createdAt,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String branchId,
                required String name,
                Value<String> fatherName = const Value.absent(),
                Value<String> mobile = const Value.absent(),
                Value<String> address = const Value.absent(),
                Value<String> skill = const Value.absent(),
                Value<String?> photoPath = const Value.absent(),
                required String joinDate,
                Value<String?> leaveDate = const Value.absent(),
                Value<String> status = const Value.absent(),
                Value<String> note = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => LaboursCompanion.insert(
                id: id,
                branchId: branchId,
                name: name,
                fatherName: fatherName,
                mobile: mobile,
                address: address,
                skill: skill,
                photoPath: photoPath,
                joinDate: joinDate,
                leaveDate: leaveDate,
                status: status,
                note: note,
                createdAt: createdAt,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$LaboursTable, Labour>(table),
                  $$LaboursTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback:
              ({
                branchId = false,
                labourRatesRefs = false,
                labourDocumentsRefs = false,
                assignmentsRefs = false,
                attendanceRefs = false,
                paymentsRefs = false,
                invoiceLinesRefs = false,
              }) {
                return PrefetchHooks(
                  db: db,
                  explicitlyWatchedTables: [
                    if (labourRatesRefs) db.labourRates,
                    if (labourDocumentsRefs) db.labourDocuments,
                    if (assignmentsRefs) db.assignments,
                    if (attendanceRefs) db.attendance,
                    if (paymentsRefs) db.payments,
                    if (invoiceLinesRefs) db.invoiceLines,
                  ],
                  addJoins:
                      <
                        T extends TableManagerState<
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic
                        >
                      >(state) {
                        if (branchId) {
                          state = state.withJoin(
                            currentTable: table,
                            currentColumn: table.branchId,
                            referencedTable: $$LaboursTableReferences
                                ._branchIdTable(db),
                            referencedColumn: $$LaboursTableReferences
                                ._branchIdTable(db)
                                .id,
                          ) as T;
                        }

                        return state;
                      },
                  getPrefetchedDataCallback: (items) async {
                    return [
                      if (labourRatesRefs)
                        await $_getPrefetchedData<
                          Labour,
                          $LaboursTable,
                          LabourRate
                        >(
                          currentTable: table,
                          referencedTable: $$LaboursTableReferences
                              ._labourRatesRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$LaboursTableReferences(
                                db,
                                table,
                                p0,
                              ).labourRatesRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.labourId == item.id,
                              ),
                          typedResults: items,
                        ),
                      if (labourDocumentsRefs)
                        await $_getPrefetchedData<
                          Labour,
                          $LaboursTable,
                          LabourDocument
                        >(
                          currentTable: table,
                          referencedTable: $$LaboursTableReferences
                              ._labourDocumentsRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$LaboursTableReferences(
                                db,
                                table,
                                p0,
                              ).labourDocumentsRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.labourId == item.id,
                              ),
                          typedResults: items,
                        ),
                      if (assignmentsRefs)
                        await $_getPrefetchedData<
                          Labour,
                          $LaboursTable,
                          Assignment
                        >(
                          currentTable: table,
                          referencedTable: $$LaboursTableReferences
                              ._assignmentsRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$LaboursTableReferences(
                                db,
                                table,
                                p0,
                              ).assignmentsRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.labourId == item.id,
                              ),
                          typedResults: items,
                        ),
                      if (attendanceRefs)
                        await $_getPrefetchedData<
                          Labour,
                          $LaboursTable,
                          AttendanceData
                        >(
                          currentTable: table,
                          referencedTable: $$LaboursTableReferences
                              ._attendanceRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$LaboursTableReferences(
                                db,
                                table,
                                p0,
                              ).attendanceRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.labourId == item.id,
                              ),
                          typedResults: items,
                        ),
                      if (paymentsRefs)
                        await $_getPrefetchedData<
                          Labour,
                          $LaboursTable,
                          Payment
                        >(
                          currentTable: table,
                          referencedTable: $$LaboursTableReferences
                              ._paymentsRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$LaboursTableReferences(
                                db,
                                table,
                                p0,
                              ).paymentsRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.labourId == item.id,
                              ),
                          typedResults: items,
                        ),
                      if (invoiceLinesRefs)
                        await $_getPrefetchedData<
                          Labour,
                          $LaboursTable,
                          InvoiceLine
                        >(
                          currentTable: table,
                          referencedTable: $$LaboursTableReferences
                              ._invoiceLinesRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$LaboursTableReferences(
                                db,
                                table,
                                p0,
                              ).invoiceLinesRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.labourId == item.id,
                              ),
                          typedResults: items,
                        ),
                    ];
                  },
                );
              },
        ),
      );
}

typedef $$LaboursTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $LaboursTable,
      Labour,
      $$LaboursTableFilterComposer,
      $$LaboursTableOrderingComposer,
      $$LaboursTableAnnotationComposer,
      $$LaboursTableCreateCompanionBuilder,
      $$LaboursTableUpdateCompanionBuilder,
      (Labour, $$LaboursTableReferences),
      Labour,
      PrefetchHooks Function({
        bool branchId,
        bool labourRatesRefs,
        bool labourDocumentsRefs,
        bool assignmentsRefs,
        bool attendanceRefs,
        bool paymentsRefs,
        bool invoiceLinesRefs,
      })
    >;
typedef $$LabourRatesTableCreateCompanionBuilder =
    LabourRatesCompanion Function({
      required String id,
      required String labourId,
      required String effectiveFrom,
      Value<String> payType,
      required int amount,
      Value<int?> otPerHour,
      Value<int> rowid,
    });
typedef $$LabourRatesTableUpdateCompanionBuilder =
    LabourRatesCompanion Function({
      Value<String> id,
      Value<String> labourId,
      Value<String> effectiveFrom,
      Value<String> payType,
      Value<int> amount,
      Value<int?> otPerHour,
      Value<int> rowid,
    });

final class $$LabourRatesTableReferences
    extends BaseReferences<_$AppDatabase, $LabourRatesTable, LabourRate> {
  $$LabourRatesTableReferences(super.$_db, super.$_table, super.$_typedResult);

  static $LaboursTable _labourIdTable(_$AppDatabase db) =>
      db.labours.createAlias('labour_rates__labour_id__labours__id');

  $$LaboursTableProcessedTableManager get labourId {
    final $_column = $_itemColumn<String>('labour_id')!;

    final manager = $$LaboursTableTableManager(
      $_db,
      $_db.labours,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_labourIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }
}

class $$LabourRatesTableFilterComposer
    extends Composer<_$AppDatabase, $LabourRatesTable> {
  $$LabourRatesTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get effectiveFrom => $composableBuilder(
    column: $table.effectiveFrom,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get payType => $composableBuilder(
    column: $table.payType,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<int> get amount => $composableBuilder(
    column: $table.amount,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<int> get otPerHour => $composableBuilder(
    column: $table.otPerHour,
    builder: (column) => ColumnFilters(column),
  );

  $$LaboursTableFilterComposer get labourId {
    final $$LaboursTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableFilterComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$LabourRatesTableOrderingComposer
    extends Composer<_$AppDatabase, $LabourRatesTable> {
  $$LabourRatesTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get effectiveFrom => $composableBuilder(
    column: $table.effectiveFrom,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get payType => $composableBuilder(
    column: $table.payType,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<int> get amount => $composableBuilder(
    column: $table.amount,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<int> get otPerHour => $composableBuilder(
    column: $table.otPerHour,
    builder: (column) => ColumnOrderings(column),
  );

  $$LaboursTableOrderingComposer get labourId {
    final $$LaboursTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableOrderingComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$LabourRatesTableAnnotationComposer
    extends Composer<_$AppDatabase, $LabourRatesTable> {
  $$LabourRatesTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get effectiveFrom => $composableBuilder(
    column: $table.effectiveFrom,
    builder: (column) => column,
  );

  GeneratedColumn<String> get payType =>
      $composableBuilder(column: $table.payType, builder: (column) => column);

  GeneratedColumn<int> get amount =>
      $composableBuilder(column: $table.amount, builder: (column) => column);

  GeneratedColumn<int> get otPerHour =>
      $composableBuilder(column: $table.otPerHour, builder: (column) => column);

  $$LaboursTableAnnotationComposer get labourId {
    final $$LaboursTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableAnnotationComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$LabourRatesTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $LabourRatesTable,
          LabourRate,
          $$LabourRatesTableFilterComposer,
          $$LabourRatesTableOrderingComposer,
          $$LabourRatesTableAnnotationComposer,
          $$LabourRatesTableCreateCompanionBuilder,
          $$LabourRatesTableUpdateCompanionBuilder,
          (LabourRate, $$LabourRatesTableReferences),
          LabourRate,
          PrefetchHooks Function({bool labourId})
        > {
  $$LabourRatesTableTableManager(_$AppDatabase db, $LabourRatesTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$LabourRatesTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$LabourRatesTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$LabourRatesTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> labourId = const Value.absent(),
                Value<String> effectiveFrom = const Value.absent(),
                Value<String> payType = const Value.absent(),
                Value<int> amount = const Value.absent(),
                Value<int?> otPerHour = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => LabourRatesCompanion(
                id: id,
                labourId: labourId,
                effectiveFrom: effectiveFrom,
                payType: payType,
                amount: amount,
                otPerHour: otPerHour,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String labourId,
                required String effectiveFrom,
                Value<String> payType = const Value.absent(),
                required int amount,
                Value<int?> otPerHour = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => LabourRatesCompanion.insert(
                id: id,
                labourId: labourId,
                effectiveFrom: effectiveFrom,
                payType: payType,
                amount: amount,
                otPerHour: otPerHour,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$LabourRatesTable, LabourRate>(table),
                  $$LabourRatesTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback: ({labourId = false}) {
            return PrefetchHooks(
              db: db,
              explicitlyWatchedTables: [],
              addJoins:
                  <
                    T extends TableManagerState<
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic
                    >
                  >(state) {
                    if (labourId) {
                      state = state.withJoin(
                        currentTable: table,
                        currentColumn: table.labourId,
                        referencedTable: $$LabourRatesTableReferences
                            ._labourIdTable(db),
                        referencedColumn: $$LabourRatesTableReferences
                            ._labourIdTable(db)
                            .id,
                      ) as T;
                    }

                    return state;
                  },
              getPrefetchedDataCallback: (items) async {
                return [];
              },
            );
          },
        ),
      );
}

typedef $$LabourRatesTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $LabourRatesTable,
      LabourRate,
      $$LabourRatesTableFilterComposer,
      $$LabourRatesTableOrderingComposer,
      $$LabourRatesTableAnnotationComposer,
      $$LabourRatesTableCreateCompanionBuilder,
      $$LabourRatesTableUpdateCompanionBuilder,
      (LabourRate, $$LabourRatesTableReferences),
      LabourRate,
      PrefetchHooks Function({bool labourId})
    >;
typedef $$LabourDocumentsTableCreateCompanionBuilder =
    LabourDocumentsCompanion Function({
      required String id,
      required String labourId,
      required String docType,
      Value<String> docNumber,
      Value<String?> filePath,
      Value<String?> expiryDate,
      Value<DateTime> createdAt,
      Value<int> rowid,
    });
typedef $$LabourDocumentsTableUpdateCompanionBuilder =
    LabourDocumentsCompanion Function({
      Value<String> id,
      Value<String> labourId,
      Value<String> docType,
      Value<String> docNumber,
      Value<String?> filePath,
      Value<String?> expiryDate,
      Value<DateTime> createdAt,
      Value<int> rowid,
    });

final class $$LabourDocumentsTableReferences
    extends
        BaseReferences<_$AppDatabase, $LabourDocumentsTable, LabourDocument> {
  $$LabourDocumentsTableReferences(
    super.$_db,
    super.$_table,
    super.$_typedResult,
  );

  static $LaboursTable _labourIdTable(_$AppDatabase db) =>
      db.labours.createAlias('labour_documents__labour_id__labours__id');

  $$LaboursTableProcessedTableManager get labourId {
    final $_column = $_itemColumn<String>('labour_id')!;

    final manager = $$LaboursTableTableManager(
      $_db,
      $_db.labours,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_labourIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }
}

class $$LabourDocumentsTableFilterComposer
    extends Composer<_$AppDatabase, $LabourDocumentsTable> {
  $$LabourDocumentsTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get docType => $composableBuilder(
    column: $table.docType,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get docNumber => $composableBuilder(
    column: $table.docNumber,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get filePath => $composableBuilder(
    column: $table.filePath,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get expiryDate => $composableBuilder(
    column: $table.expiryDate,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnFilters(column),
  );

  $$LaboursTableFilterComposer get labourId {
    final $$LaboursTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableFilterComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$LabourDocumentsTableOrderingComposer
    extends Composer<_$AppDatabase, $LabourDocumentsTable> {
  $$LabourDocumentsTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get docType => $composableBuilder(
    column: $table.docType,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get docNumber => $composableBuilder(
    column: $table.docNumber,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get filePath => $composableBuilder(
    column: $table.filePath,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get expiryDate => $composableBuilder(
    column: $table.expiryDate,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnOrderings(column),
  );

  $$LaboursTableOrderingComposer get labourId {
    final $$LaboursTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableOrderingComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$LabourDocumentsTableAnnotationComposer
    extends Composer<_$AppDatabase, $LabourDocumentsTable> {
  $$LabourDocumentsTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get docType =>
      $composableBuilder(column: $table.docType, builder: (column) => column);

  GeneratedColumn<String> get docNumber =>
      $composableBuilder(column: $table.docNumber, builder: (column) => column);

  GeneratedColumn<String> get filePath =>
      $composableBuilder(column: $table.filePath, builder: (column) => column);

  GeneratedColumn<String> get expiryDate => $composableBuilder(
    column: $table.expiryDate,
    builder: (column) => column,
  );

  GeneratedColumn<DateTime> get createdAt =>
      $composableBuilder(column: $table.createdAt, builder: (column) => column);

  $$LaboursTableAnnotationComposer get labourId {
    final $$LaboursTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableAnnotationComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$LabourDocumentsTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $LabourDocumentsTable,
          LabourDocument,
          $$LabourDocumentsTableFilterComposer,
          $$LabourDocumentsTableOrderingComposer,
          $$LabourDocumentsTableAnnotationComposer,
          $$LabourDocumentsTableCreateCompanionBuilder,
          $$LabourDocumentsTableUpdateCompanionBuilder,
          (LabourDocument, $$LabourDocumentsTableReferences),
          LabourDocument,
          PrefetchHooks Function({bool labourId})
        > {
  $$LabourDocumentsTableTableManager(
    _$AppDatabase db,
    $LabourDocumentsTable table,
  ) : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$LabourDocumentsTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$LabourDocumentsTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$LabourDocumentsTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> labourId = const Value.absent(),
                Value<String> docType = const Value.absent(),
                Value<String> docNumber = const Value.absent(),
                Value<String?> filePath = const Value.absent(),
                Value<String?> expiryDate = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => LabourDocumentsCompanion(
                id: id,
                labourId: labourId,
                docType: docType,
                docNumber: docNumber,
                filePath: filePath,
                expiryDate: expiryDate,
                createdAt: createdAt,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String labourId,
                required String docType,
                Value<String> docNumber = const Value.absent(),
                Value<String?> filePath = const Value.absent(),
                Value<String?> expiryDate = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => LabourDocumentsCompanion.insert(
                id: id,
                labourId: labourId,
                docType: docType,
                docNumber: docNumber,
                filePath: filePath,
                expiryDate: expiryDate,
                createdAt: createdAt,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$LabourDocumentsTable, LabourDocument>(table),
                  $$LabourDocumentsTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback: ({labourId = false}) {
            return PrefetchHooks(
              db: db,
              explicitlyWatchedTables: [],
              addJoins:
                  <
                    T extends TableManagerState<
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic
                    >
                  >(state) {
                    if (labourId) {
                      state = state.withJoin(
                        currentTable: table,
                        currentColumn: table.labourId,
                        referencedTable: $$LabourDocumentsTableReferences
                            ._labourIdTable(db),
                        referencedColumn: $$LabourDocumentsTableReferences
                            ._labourIdTable(db)
                            .id,
                      ) as T;
                    }

                    return state;
                  },
              getPrefetchedDataCallback: (items) async {
                return [];
              },
            );
          },
        ),
      );
}

typedef $$LabourDocumentsTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $LabourDocumentsTable,
      LabourDocument,
      $$LabourDocumentsTableFilterComposer,
      $$LabourDocumentsTableOrderingComposer,
      $$LabourDocumentsTableAnnotationComposer,
      $$LabourDocumentsTableCreateCompanionBuilder,
      $$LabourDocumentsTableUpdateCompanionBuilder,
      (LabourDocument, $$LabourDocumentsTableReferences),
      LabourDocument,
      PrefetchHooks Function({bool labourId})
    >;
typedef $$AssignmentsTableCreateCompanionBuilder =
    AssignmentsCompanion Function({
      required String id,
      required String labourId,
      required String siteId,
      required String fromDate,
      Value<String?> toDate,
      Value<int> rowid,
    });
typedef $$AssignmentsTableUpdateCompanionBuilder =
    AssignmentsCompanion Function({
      Value<String> id,
      Value<String> labourId,
      Value<String> siteId,
      Value<String> fromDate,
      Value<String?> toDate,
      Value<int> rowid,
    });

final class $$AssignmentsTableReferences
    extends BaseReferences<_$AppDatabase, $AssignmentsTable, Assignment> {
  $$AssignmentsTableReferences(super.$_db, super.$_table, super.$_typedResult);

  static $LaboursTable _labourIdTable(_$AppDatabase db) =>
      db.labours.createAlias('assignments__labour_id__labours__id');

  $$LaboursTableProcessedTableManager get labourId {
    final $_column = $_itemColumn<String>('labour_id')!;

    final manager = $$LaboursTableTableManager(
      $_db,
      $_db.labours,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_labourIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }

  static $SitesTable _siteIdTable(_$AppDatabase db) =>
      db.sites.createAlias('assignments__site_id__sites__id');

  $$SitesTableProcessedTableManager get siteId {
    final $_column = $_itemColumn<String>('site_id')!;

    final manager = $$SitesTableTableManager(
      $_db,
      $_db.sites,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_siteIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }
}

class $$AssignmentsTableFilterComposer
    extends Composer<_$AppDatabase, $AssignmentsTable> {
  $$AssignmentsTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get fromDate => $composableBuilder(
    column: $table.fromDate,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get toDate => $composableBuilder(
    column: $table.toDate,
    builder: (column) => ColumnFilters(column),
  );

  $$LaboursTableFilterComposer get labourId {
    final $$LaboursTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableFilterComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$SitesTableFilterComposer get siteId {
    final $$SitesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.siteId,
      referencedTable: $db.sites,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SitesTableFilterComposer(
            $db: $db,
            $table: $db.sites,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$AssignmentsTableOrderingComposer
    extends Composer<_$AppDatabase, $AssignmentsTable> {
  $$AssignmentsTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get fromDate => $composableBuilder(
    column: $table.fromDate,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get toDate => $composableBuilder(
    column: $table.toDate,
    builder: (column) => ColumnOrderings(column),
  );

  $$LaboursTableOrderingComposer get labourId {
    final $$LaboursTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableOrderingComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$SitesTableOrderingComposer get siteId {
    final $$SitesTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.siteId,
      referencedTable: $db.sites,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SitesTableOrderingComposer(
            $db: $db,
            $table: $db.sites,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$AssignmentsTableAnnotationComposer
    extends Composer<_$AppDatabase, $AssignmentsTable> {
  $$AssignmentsTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get fromDate =>
      $composableBuilder(column: $table.fromDate, builder: (column) => column);

  GeneratedColumn<String> get toDate =>
      $composableBuilder(column: $table.toDate, builder: (column) => column);

  $$LaboursTableAnnotationComposer get labourId {
    final $$LaboursTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableAnnotationComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$SitesTableAnnotationComposer get siteId {
    final $$SitesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.siteId,
      referencedTable: $db.sites,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SitesTableAnnotationComposer(
            $db: $db,
            $table: $db.sites,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$AssignmentsTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $AssignmentsTable,
          Assignment,
          $$AssignmentsTableFilterComposer,
          $$AssignmentsTableOrderingComposer,
          $$AssignmentsTableAnnotationComposer,
          $$AssignmentsTableCreateCompanionBuilder,
          $$AssignmentsTableUpdateCompanionBuilder,
          (Assignment, $$AssignmentsTableReferences),
          Assignment,
          PrefetchHooks Function({bool labourId, bool siteId})
        > {
  $$AssignmentsTableTableManager(_$AppDatabase db, $AssignmentsTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$AssignmentsTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$AssignmentsTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$AssignmentsTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> labourId = const Value.absent(),
                Value<String> siteId = const Value.absent(),
                Value<String> fromDate = const Value.absent(),
                Value<String?> toDate = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => AssignmentsCompanion(
                id: id,
                labourId: labourId,
                siteId: siteId,
                fromDate: fromDate,
                toDate: toDate,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String labourId,
                required String siteId,
                required String fromDate,
                Value<String?> toDate = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => AssignmentsCompanion.insert(
                id: id,
                labourId: labourId,
                siteId: siteId,
                fromDate: fromDate,
                toDate: toDate,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$AssignmentsTable, Assignment>(table),
                  $$AssignmentsTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback: ({labourId = false, siteId = false}) {
            return PrefetchHooks(
              db: db,
              explicitlyWatchedTables: [],
              addJoins:
                  <
                    T extends TableManagerState<
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic
                    >
                  >(state) {
                    if (labourId) {
                      state = state.withJoin(
                        currentTable: table,
                        currentColumn: table.labourId,
                        referencedTable: $$AssignmentsTableReferences
                            ._labourIdTable(db),
                        referencedColumn: $$AssignmentsTableReferences
                            ._labourIdTable(db)
                            .id,
                      ) as T;
                    }
                    if (siteId) {
                      state = state.withJoin(
                        currentTable: table,
                        currentColumn: table.siteId,
                        referencedTable: $$AssignmentsTableReferences
                            ._siteIdTable(db),
                        referencedColumn: $$AssignmentsTableReferences
                            ._siteIdTable(db)
                            .id,
                      ) as T;
                    }

                    return state;
                  },
              getPrefetchedDataCallback: (items) async {
                return [];
              },
            );
          },
        ),
      );
}

typedef $$AssignmentsTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $AssignmentsTable,
      Assignment,
      $$AssignmentsTableFilterComposer,
      $$AssignmentsTableOrderingComposer,
      $$AssignmentsTableAnnotationComposer,
      $$AssignmentsTableCreateCompanionBuilder,
      $$AssignmentsTableUpdateCompanionBuilder,
      (Assignment, $$AssignmentsTableReferences),
      Assignment,
      PrefetchHooks Function({bool labourId, bool siteId})
    >;
typedef $$AttendanceTableCreateCompanionBuilder = AttendanceCompanion Function({
  required String id,
  required String labourId,
  required String siteId,
  required String date,
  required String status,
  Value<double> otHours,
  Value<String> note,
  Value<String?> markedBy,
  Value<DateTime> markedAt,
  Value<bool> isLate,
  Value<int> rowid,
});
typedef $$AttendanceTableUpdateCompanionBuilder = AttendanceCompanion Function({
  Value<String> id,
  Value<String> labourId,
  Value<String> siteId,
  Value<String> date,
  Value<String> status,
  Value<double> otHours,
  Value<String> note,
  Value<String?> markedBy,
  Value<DateTime> markedAt,
  Value<bool> isLate,
  Value<int> rowid,
});

final class $$AttendanceTableReferences
    extends BaseReferences<_$AppDatabase, $AttendanceTable, AttendanceData> {
  $$AttendanceTableReferences(super.$_db, super.$_table, super.$_typedResult);

  static $LaboursTable _labourIdTable(_$AppDatabase db) =>
      db.labours.createAlias('attendance__labour_id__labours__id');

  $$LaboursTableProcessedTableManager get labourId {
    final $_column = $_itemColumn<String>('labour_id')!;

    final manager = $$LaboursTableTableManager(
      $_db,
      $_db.labours,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_labourIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }

  static $SitesTable _siteIdTable(_$AppDatabase db) =>
      db.sites.createAlias('attendance__site_id__sites__id');

  $$SitesTableProcessedTableManager get siteId {
    final $_column = $_itemColumn<String>('site_id')!;

    final manager = $$SitesTableTableManager(
      $_db,
      $_db.sites,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_siteIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }
}

class $$AttendanceTableFilterComposer
    extends Composer<_$AppDatabase, $AttendanceTable> {
  $$AttendanceTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get date => $composableBuilder(
    column: $table.date,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get status => $composableBuilder(
    column: $table.status,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<double> get otHours => $composableBuilder(
    column: $table.otHours,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get note => $composableBuilder(
    column: $table.note,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get markedBy => $composableBuilder(
    column: $table.markedBy,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get markedAt => $composableBuilder(
    column: $table.markedAt,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<bool> get isLate => $composableBuilder(
    column: $table.isLate,
    builder: (column) => ColumnFilters(column),
  );

  $$LaboursTableFilterComposer get labourId {
    final $$LaboursTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableFilterComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$SitesTableFilterComposer get siteId {
    final $$SitesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.siteId,
      referencedTable: $db.sites,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SitesTableFilterComposer(
            $db: $db,
            $table: $db.sites,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$AttendanceTableOrderingComposer
    extends Composer<_$AppDatabase, $AttendanceTable> {
  $$AttendanceTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get date => $composableBuilder(
    column: $table.date,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get status => $composableBuilder(
    column: $table.status,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<double> get otHours => $composableBuilder(
    column: $table.otHours,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get note => $composableBuilder(
    column: $table.note,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get markedBy => $composableBuilder(
    column: $table.markedBy,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get markedAt => $composableBuilder(
    column: $table.markedAt,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<bool> get isLate => $composableBuilder(
    column: $table.isLate,
    builder: (column) => ColumnOrderings(column),
  );

  $$LaboursTableOrderingComposer get labourId {
    final $$LaboursTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableOrderingComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$SitesTableOrderingComposer get siteId {
    final $$SitesTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.siteId,
      referencedTable: $db.sites,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SitesTableOrderingComposer(
            $db: $db,
            $table: $db.sites,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$AttendanceTableAnnotationComposer
    extends Composer<_$AppDatabase, $AttendanceTable> {
  $$AttendanceTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get date =>
      $composableBuilder(column: $table.date, builder: (column) => column);

  GeneratedColumn<String> get status =>
      $composableBuilder(column: $table.status, builder: (column) => column);

  GeneratedColumn<double> get otHours =>
      $composableBuilder(column: $table.otHours, builder: (column) => column);

  GeneratedColumn<String> get note =>
      $composableBuilder(column: $table.note, builder: (column) => column);

  GeneratedColumn<String> get markedBy =>
      $composableBuilder(column: $table.markedBy, builder: (column) => column);

  GeneratedColumn<DateTime> get markedAt =>
      $composableBuilder(column: $table.markedAt, builder: (column) => column);

  GeneratedColumn<bool> get isLate =>
      $composableBuilder(column: $table.isLate, builder: (column) => column);

  $$LaboursTableAnnotationComposer get labourId {
    final $$LaboursTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableAnnotationComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$SitesTableAnnotationComposer get siteId {
    final $$SitesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.siteId,
      referencedTable: $db.sites,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SitesTableAnnotationComposer(
            $db: $db,
            $table: $db.sites,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$AttendanceTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $AttendanceTable,
          AttendanceData,
          $$AttendanceTableFilterComposer,
          $$AttendanceTableOrderingComposer,
          $$AttendanceTableAnnotationComposer,
          $$AttendanceTableCreateCompanionBuilder,
          $$AttendanceTableUpdateCompanionBuilder,
          (AttendanceData, $$AttendanceTableReferences),
          AttendanceData,
          PrefetchHooks Function({bool labourId, bool siteId})
        > {
  $$AttendanceTableTableManager(_$AppDatabase db, $AttendanceTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$AttendanceTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$AttendanceTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$AttendanceTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> labourId = const Value.absent(),
                Value<String> siteId = const Value.absent(),
                Value<String> date = const Value.absent(),
                Value<String> status = const Value.absent(),
                Value<double> otHours = const Value.absent(),
                Value<String> note = const Value.absent(),
                Value<String?> markedBy = const Value.absent(),
                Value<DateTime> markedAt = const Value.absent(),
                Value<bool> isLate = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => AttendanceCompanion(
                id: id,
                labourId: labourId,
                siteId: siteId,
                date: date,
                status: status,
                otHours: otHours,
                note: note,
                markedBy: markedBy,
                markedAt: markedAt,
                isLate: isLate,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String labourId,
                required String siteId,
                required String date,
                required String status,
                Value<double> otHours = const Value.absent(),
                Value<String> note = const Value.absent(),
                Value<String?> markedBy = const Value.absent(),
                Value<DateTime> markedAt = const Value.absent(),
                Value<bool> isLate = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => AttendanceCompanion.insert(
                id: id,
                labourId: labourId,
                siteId: siteId,
                date: date,
                status: status,
                otHours: otHours,
                note: note,
                markedBy: markedBy,
                markedAt: markedAt,
                isLate: isLate,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$AttendanceTable, AttendanceData>(table),
                  $$AttendanceTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback: ({labourId = false, siteId = false}) {
            return PrefetchHooks(
              db: db,
              explicitlyWatchedTables: [],
              addJoins:
                  <
                    T extends TableManagerState<
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic
                    >
                  >(state) {
                    if (labourId) {
                      state = state.withJoin(
                        currentTable: table,
                        currentColumn: table.labourId,
                        referencedTable: $$AttendanceTableReferences
                            ._labourIdTable(db),
                        referencedColumn: $$AttendanceTableReferences
                            ._labourIdTable(db)
                            .id,
                      ) as T;
                    }
                    if (siteId) {
                      state = state.withJoin(
                        currentTable: table,
                        currentColumn: table.siteId,
                        referencedTable: $$AttendanceTableReferences
                            ._siteIdTable(db),
                        referencedColumn: $$AttendanceTableReferences
                            ._siteIdTable(db)
                            .id,
                      ) as T;
                    }

                    return state;
                  },
              getPrefetchedDataCallback: (items) async {
                return [];
              },
            );
          },
        ),
      );
}

typedef $$AttendanceTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $AttendanceTable,
      AttendanceData,
      $$AttendanceTableFilterComposer,
      $$AttendanceTableOrderingComposer,
      $$AttendanceTableAnnotationComposer,
      $$AttendanceTableCreateCompanionBuilder,
      $$AttendanceTableUpdateCompanionBuilder,
      (AttendanceData, $$AttendanceTableReferences),
      AttendanceData,
      PrefetchHooks Function({bool labourId, bool siteId})
    >;
typedef $$AttendanceSheetsTableCreateCompanionBuilder =
    AttendanceSheetsCompanion Function({
      required String id,
      required String siteId,
      required String date,
      Value<String?> submittedBy,
      Value<DateTime> submittedAt,
      Value<int> rowid,
    });
typedef $$AttendanceSheetsTableUpdateCompanionBuilder =
    AttendanceSheetsCompanion Function({
      Value<String> id,
      Value<String> siteId,
      Value<String> date,
      Value<String?> submittedBy,
      Value<DateTime> submittedAt,
      Value<int> rowid,
    });

final class $$AttendanceSheetsTableReferences
    extends
        BaseReferences<_$AppDatabase, $AttendanceSheetsTable, AttendanceSheet> {
  $$AttendanceSheetsTableReferences(
    super.$_db,
    super.$_table,
    super.$_typedResult,
  );

  static $SitesTable _siteIdTable(_$AppDatabase db) =>
      db.sites.createAlias('attendance_sheets__site_id__sites__id');

  $$SitesTableProcessedTableManager get siteId {
    final $_column = $_itemColumn<String>('site_id')!;

    final manager = $$SitesTableTableManager(
      $_db,
      $_db.sites,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_siteIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }
}

class $$AttendanceSheetsTableFilterComposer
    extends Composer<_$AppDatabase, $AttendanceSheetsTable> {
  $$AttendanceSheetsTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get date => $composableBuilder(
    column: $table.date,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get submittedBy => $composableBuilder(
    column: $table.submittedBy,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get submittedAt => $composableBuilder(
    column: $table.submittedAt,
    builder: (column) => ColumnFilters(column),
  );

  $$SitesTableFilterComposer get siteId {
    final $$SitesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.siteId,
      referencedTable: $db.sites,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SitesTableFilterComposer(
            $db: $db,
            $table: $db.sites,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$AttendanceSheetsTableOrderingComposer
    extends Composer<_$AppDatabase, $AttendanceSheetsTable> {
  $$AttendanceSheetsTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get date => $composableBuilder(
    column: $table.date,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get submittedBy => $composableBuilder(
    column: $table.submittedBy,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get submittedAt => $composableBuilder(
    column: $table.submittedAt,
    builder: (column) => ColumnOrderings(column),
  );

  $$SitesTableOrderingComposer get siteId {
    final $$SitesTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.siteId,
      referencedTable: $db.sites,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SitesTableOrderingComposer(
            $db: $db,
            $table: $db.sites,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$AttendanceSheetsTableAnnotationComposer
    extends Composer<_$AppDatabase, $AttendanceSheetsTable> {
  $$AttendanceSheetsTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get date =>
      $composableBuilder(column: $table.date, builder: (column) => column);

  GeneratedColumn<String> get submittedBy => $composableBuilder(
    column: $table.submittedBy,
    builder: (column) => column,
  );

  GeneratedColumn<DateTime> get submittedAt => $composableBuilder(
    column: $table.submittedAt,
    builder: (column) => column,
  );

  $$SitesTableAnnotationComposer get siteId {
    final $$SitesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.siteId,
      referencedTable: $db.sites,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SitesTableAnnotationComposer(
            $db: $db,
            $table: $db.sites,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$AttendanceSheetsTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $AttendanceSheetsTable,
          AttendanceSheet,
          $$AttendanceSheetsTableFilterComposer,
          $$AttendanceSheetsTableOrderingComposer,
          $$AttendanceSheetsTableAnnotationComposer,
          $$AttendanceSheetsTableCreateCompanionBuilder,
          $$AttendanceSheetsTableUpdateCompanionBuilder,
          (AttendanceSheet, $$AttendanceSheetsTableReferences),
          AttendanceSheet,
          PrefetchHooks Function({bool siteId})
        > {
  $$AttendanceSheetsTableTableManager(
    _$AppDatabase db,
    $AttendanceSheetsTable table,
  ) : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$AttendanceSheetsTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$AttendanceSheetsTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$AttendanceSheetsTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> siteId = const Value.absent(),
                Value<String> date = const Value.absent(),
                Value<String?> submittedBy = const Value.absent(),
                Value<DateTime> submittedAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => AttendanceSheetsCompanion(
                id: id,
                siteId: siteId,
                date: date,
                submittedBy: submittedBy,
                submittedAt: submittedAt,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String siteId,
                required String date,
                Value<String?> submittedBy = const Value.absent(),
                Value<DateTime> submittedAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => AttendanceSheetsCompanion.insert(
                id: id,
                siteId: siteId,
                date: date,
                submittedBy: submittedBy,
                submittedAt: submittedAt,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$AttendanceSheetsTable, AttendanceSheet>(table),
                  $$AttendanceSheetsTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback: ({siteId = false}) {
            return PrefetchHooks(
              db: db,
              explicitlyWatchedTables: [],
              addJoins:
                  <
                    T extends TableManagerState<
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic
                    >
                  >(state) {
                    if (siteId) {
                      state = state.withJoin(
                        currentTable: table,
                        currentColumn: table.siteId,
                        referencedTable: $$AttendanceSheetsTableReferences
                            ._siteIdTable(db),
                        referencedColumn: $$AttendanceSheetsTableReferences
                            ._siteIdTable(db)
                            .id,
                      ) as T;
                    }

                    return state;
                  },
              getPrefetchedDataCallback: (items) async {
                return [];
              },
            );
          },
        ),
      );
}

typedef $$AttendanceSheetsTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $AttendanceSheetsTable,
      AttendanceSheet,
      $$AttendanceSheetsTableFilterComposer,
      $$AttendanceSheetsTableOrderingComposer,
      $$AttendanceSheetsTableAnnotationComposer,
      $$AttendanceSheetsTableCreateCompanionBuilder,
      $$AttendanceSheetsTableUpdateCompanionBuilder,
      (AttendanceSheet, $$AttendanceSheetsTableReferences),
      AttendanceSheet,
      PrefetchHooks Function({bool siteId})
    >;
typedef $$PaymentsTableCreateCompanionBuilder = PaymentsCompanion Function({
  required String id,
  required String branchId,
  required String labourId,
  required String type,
  Value<String> mode,
  required int amount,
  required String date,
  Value<String> note,
  Value<String> reference,
  Value<String?> createdBy,
  Value<DateTime> createdAt,
  Value<DateTime?> voidedAt,
  Value<String?> voidReason,
  Value<int> rowid,
});
typedef $$PaymentsTableUpdateCompanionBuilder = PaymentsCompanion Function({
  Value<String> id,
  Value<String> branchId,
  Value<String> labourId,
  Value<String> type,
  Value<String> mode,
  Value<int> amount,
  Value<String> date,
  Value<String> note,
  Value<String> reference,
  Value<String?> createdBy,
  Value<DateTime> createdAt,
  Value<DateTime?> voidedAt,
  Value<String?> voidReason,
  Value<int> rowid,
});

final class $$PaymentsTableReferences
    extends BaseReferences<_$AppDatabase, $PaymentsTable, Payment> {
  $$PaymentsTableReferences(super.$_db, super.$_table, super.$_typedResult);

  static $BranchesTable _branchIdTable(_$AppDatabase db) =>
      db.branches.createAlias('payments__branch_id__branches__id');

  $$BranchesTableProcessedTableManager get branchId {
    final $_column = $_itemColumn<String>('branch_id')!;

    final manager = $$BranchesTableTableManager(
      $_db,
      $_db.branches,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_branchIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }

  static $LaboursTable _labourIdTable(_$AppDatabase db) =>
      db.labours.createAlias('payments__labour_id__labours__id');

  $$LaboursTableProcessedTableManager get labourId {
    final $_column = $_itemColumn<String>('labour_id')!;

    final manager = $$LaboursTableTableManager(
      $_db,
      $_db.labours,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_labourIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }
}

class $$PaymentsTableFilterComposer
    extends Composer<_$AppDatabase, $PaymentsTable> {
  $$PaymentsTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get type => $composableBuilder(
    column: $table.type,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get mode => $composableBuilder(
    column: $table.mode,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<int> get amount => $composableBuilder(
    column: $table.amount,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get date => $composableBuilder(
    column: $table.date,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get note => $composableBuilder(
    column: $table.note,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get reference => $composableBuilder(
    column: $table.reference,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get createdBy => $composableBuilder(
    column: $table.createdBy,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get voidedAt => $composableBuilder(
    column: $table.voidedAt,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get voidReason => $composableBuilder(
    column: $table.voidReason,
    builder: (column) => ColumnFilters(column),
  );

  $$BranchesTableFilterComposer get branchId {
    final $$BranchesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.branchId,
      referencedTable: $db.branches,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$BranchesTableFilterComposer(
            $db: $db,
            $table: $db.branches,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$LaboursTableFilterComposer get labourId {
    final $$LaboursTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableFilterComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$PaymentsTableOrderingComposer
    extends Composer<_$AppDatabase, $PaymentsTable> {
  $$PaymentsTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get type => $composableBuilder(
    column: $table.type,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get mode => $composableBuilder(
    column: $table.mode,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<int> get amount => $composableBuilder(
    column: $table.amount,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get date => $composableBuilder(
    column: $table.date,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get note => $composableBuilder(
    column: $table.note,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get reference => $composableBuilder(
    column: $table.reference,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get createdBy => $composableBuilder(
    column: $table.createdBy,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get voidedAt => $composableBuilder(
    column: $table.voidedAt,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get voidReason => $composableBuilder(
    column: $table.voidReason,
    builder: (column) => ColumnOrderings(column),
  );

  $$BranchesTableOrderingComposer get branchId {
    final $$BranchesTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.branchId,
      referencedTable: $db.branches,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$BranchesTableOrderingComposer(
            $db: $db,
            $table: $db.branches,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$LaboursTableOrderingComposer get labourId {
    final $$LaboursTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableOrderingComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$PaymentsTableAnnotationComposer
    extends Composer<_$AppDatabase, $PaymentsTable> {
  $$PaymentsTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get type =>
      $composableBuilder(column: $table.type, builder: (column) => column);

  GeneratedColumn<String> get mode =>
      $composableBuilder(column: $table.mode, builder: (column) => column);

  GeneratedColumn<int> get amount =>
      $composableBuilder(column: $table.amount, builder: (column) => column);

  GeneratedColumn<String> get date =>
      $composableBuilder(column: $table.date, builder: (column) => column);

  GeneratedColumn<String> get note =>
      $composableBuilder(column: $table.note, builder: (column) => column);

  GeneratedColumn<String> get reference =>
      $composableBuilder(column: $table.reference, builder: (column) => column);

  GeneratedColumn<String> get createdBy =>
      $composableBuilder(column: $table.createdBy, builder: (column) => column);

  GeneratedColumn<DateTime> get createdAt =>
      $composableBuilder(column: $table.createdAt, builder: (column) => column);

  GeneratedColumn<DateTime> get voidedAt =>
      $composableBuilder(column: $table.voidedAt, builder: (column) => column);

  GeneratedColumn<String> get voidReason => $composableBuilder(
    column: $table.voidReason,
    builder: (column) => column,
  );

  $$BranchesTableAnnotationComposer get branchId {
    final $$BranchesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.branchId,
      referencedTable: $db.branches,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$BranchesTableAnnotationComposer(
            $db: $db,
            $table: $db.branches,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$LaboursTableAnnotationComposer get labourId {
    final $$LaboursTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableAnnotationComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$PaymentsTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $PaymentsTable,
          Payment,
          $$PaymentsTableFilterComposer,
          $$PaymentsTableOrderingComposer,
          $$PaymentsTableAnnotationComposer,
          $$PaymentsTableCreateCompanionBuilder,
          $$PaymentsTableUpdateCompanionBuilder,
          (Payment, $$PaymentsTableReferences),
          Payment,
          PrefetchHooks Function({bool branchId, bool labourId})
        > {
  $$PaymentsTableTableManager(_$AppDatabase db, $PaymentsTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$PaymentsTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$PaymentsTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$PaymentsTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> branchId = const Value.absent(),
                Value<String> labourId = const Value.absent(),
                Value<String> type = const Value.absent(),
                Value<String> mode = const Value.absent(),
                Value<int> amount = const Value.absent(),
                Value<String> date = const Value.absent(),
                Value<String> note = const Value.absent(),
                Value<String> reference = const Value.absent(),
                Value<String?> createdBy = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<DateTime?> voidedAt = const Value.absent(),
                Value<String?> voidReason = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => PaymentsCompanion(
                id: id,
                branchId: branchId,
                labourId: labourId,
                type: type,
                mode: mode,
                amount: amount,
                date: date,
                note: note,
                reference: reference,
                createdBy: createdBy,
                createdAt: createdAt,
                voidedAt: voidedAt,
                voidReason: voidReason,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String branchId,
                required String labourId,
                required String type,
                Value<String> mode = const Value.absent(),
                required int amount,
                required String date,
                Value<String> note = const Value.absent(),
                Value<String> reference = const Value.absent(),
                Value<String?> createdBy = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<DateTime?> voidedAt = const Value.absent(),
                Value<String?> voidReason = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => PaymentsCompanion.insert(
                id: id,
                branchId: branchId,
                labourId: labourId,
                type: type,
                mode: mode,
                amount: amount,
                date: date,
                note: note,
                reference: reference,
                createdBy: createdBy,
                createdAt: createdAt,
                voidedAt: voidedAt,
                voidReason: voidReason,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$PaymentsTable, Payment>(table),
                  $$PaymentsTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback: ({branchId = false, labourId = false}) {
            return PrefetchHooks(
              db: db,
              explicitlyWatchedTables: [],
              addJoins:
                  <
                    T extends TableManagerState<
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic
                    >
                  >(state) {
                    if (branchId) {
                      state = state.withJoin(
                        currentTable: table,
                        currentColumn: table.branchId,
                        referencedTable: $$PaymentsTableReferences
                            ._branchIdTable(db),
                        referencedColumn: $$PaymentsTableReferences
                            ._branchIdTable(db)
                            .id,
                      ) as T;
                    }
                    if (labourId) {
                      state = state.withJoin(
                        currentTable: table,
                        currentColumn: table.labourId,
                        referencedTable: $$PaymentsTableReferences
                            ._labourIdTable(db),
                        referencedColumn: $$PaymentsTableReferences
                            ._labourIdTable(db)
                            .id,
                      ) as T;
                    }

                    return state;
                  },
              getPrefetchedDataCallback: (items) async {
                return [];
              },
            );
          },
        ),
      );
}

typedef $$PaymentsTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $PaymentsTable,
      Payment,
      $$PaymentsTableFilterComposer,
      $$PaymentsTableOrderingComposer,
      $$PaymentsTableAnnotationComposer,
      $$PaymentsTableCreateCompanionBuilder,
      $$PaymentsTableUpdateCompanionBuilder,
      (Payment, $$PaymentsTableReferences),
      Payment,
      PrefetchHooks Function({bool branchId, bool labourId})
    >;
typedef $$InvoicesTableCreateCompanionBuilder = InvoicesCompanion Function({
  required String id,
  required String branchId,
  required String companyId,
  Value<String?> siteId,
  required String number,
  required String issueDate,
  required String dueDate,
  required String periodFrom,
  required String periodTo,
  required int subtotal,
  Value<double> taxRate,
  Value<String> taxLabel,
  Value<int> taxAmount,
  required int total,
  Value<String> status,
  Value<String> notes,
  Value<DateTime> createdAt,
  Value<int> rowid,
});
typedef $$InvoicesTableUpdateCompanionBuilder = InvoicesCompanion Function({
  Value<String> id,
  Value<String> branchId,
  Value<String> companyId,
  Value<String?> siteId,
  Value<String> number,
  Value<String> issueDate,
  Value<String> dueDate,
  Value<String> periodFrom,
  Value<String> periodTo,
  Value<int> subtotal,
  Value<double> taxRate,
  Value<String> taxLabel,
  Value<int> taxAmount,
  Value<int> total,
  Value<String> status,
  Value<String> notes,
  Value<DateTime> createdAt,
  Value<int> rowid,
});

final class $$InvoicesTableReferences
    extends BaseReferences<_$AppDatabase, $InvoicesTable, Invoice> {
  $$InvoicesTableReferences(super.$_db, super.$_table, super.$_typedResult);

  static $BranchesTable _branchIdTable(_$AppDatabase db) =>
      db.branches.createAlias('invoices__branch_id__branches__id');

  $$BranchesTableProcessedTableManager get branchId {
    final $_column = $_itemColumn<String>('branch_id')!;

    final manager = $$BranchesTableTableManager(
      $_db,
      $_db.branches,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_branchIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }

  static $CompaniesTable _companyIdTable(_$AppDatabase db) =>
      db.companies.createAlias('invoices__company_id__companies__id');

  $$CompaniesTableProcessedTableManager get companyId {
    final $_column = $_itemColumn<String>('company_id')!;

    final manager = $$CompaniesTableTableManager(
      $_db,
      $_db.companies,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_companyIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }

  static $SitesTable _siteIdTable(_$AppDatabase db) =>
      db.sites.createAlias('invoices__site_id__sites__id');

  $$SitesTableProcessedTableManager? get siteId {
    final $_column = $_itemColumn<String>('site_id');
    if ($_column == null) return null;
    final manager = $$SitesTableTableManager(
      $_db,
      $_db.sites,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_siteIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }

  static MultiTypedResultKey<$InvoiceLinesTable, List<InvoiceLine>>
  _invoiceLinesRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.invoiceLines,
    aliasName: 'invoices__id__invoice_lines__invoice_id',
  );

  $$InvoiceLinesTableProcessedTableManager get invoiceLinesRefs {
    final manager = $$InvoiceLinesTableTableManager(
      $_db,
      $_db.invoiceLines,
    ).filter((f) => f.invoiceId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(_invoiceLinesRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }

  static MultiTypedResultKey<$InvoicePaymentsTable, List<InvoicePayment>>
  _invoicePaymentsRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.invoicePayments,
    aliasName: 'invoices__id__invoice_payments__invoice_id',
  );

  $$InvoicePaymentsTableProcessedTableManager get invoicePaymentsRefs {
    final manager = $$InvoicePaymentsTableTableManager(
      $_db,
      $_db.invoicePayments,
    ).filter((f) => f.invoiceId.id.sqlEquals($_itemColumn<String>('id')!));

    final cache = $_typedResult.readTableOrNull(
      _invoicePaymentsRefsTable($_db),
    );
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }
}

class $$InvoicesTableFilterComposer
    extends Composer<_$AppDatabase, $InvoicesTable> {
  $$InvoicesTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get number => $composableBuilder(
    column: $table.number,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get issueDate => $composableBuilder(
    column: $table.issueDate,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get dueDate => $composableBuilder(
    column: $table.dueDate,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get periodFrom => $composableBuilder(
    column: $table.periodFrom,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get periodTo => $composableBuilder(
    column: $table.periodTo,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<int> get subtotal => $composableBuilder(
    column: $table.subtotal,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<double> get taxRate => $composableBuilder(
    column: $table.taxRate,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get taxLabel => $composableBuilder(
    column: $table.taxLabel,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<int> get taxAmount => $composableBuilder(
    column: $table.taxAmount,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<int> get total => $composableBuilder(
    column: $table.total,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get status => $composableBuilder(
    column: $table.status,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get notes => $composableBuilder(
    column: $table.notes,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnFilters(column),
  );

  $$BranchesTableFilterComposer get branchId {
    final $$BranchesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.branchId,
      referencedTable: $db.branches,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$BranchesTableFilterComposer(
            $db: $db,
            $table: $db.branches,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$CompaniesTableFilterComposer get companyId {
    final $$CompaniesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.companyId,
      referencedTable: $db.companies,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$CompaniesTableFilterComposer(
            $db: $db,
            $table: $db.companies,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$SitesTableFilterComposer get siteId {
    final $$SitesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.siteId,
      referencedTable: $db.sites,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SitesTableFilterComposer(
            $db: $db,
            $table: $db.sites,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  Expression<bool> invoiceLinesRefs(
    Expression<bool> Function($$InvoiceLinesTableFilterComposer f) f,
  ) {
    final $$InvoiceLinesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.invoiceLines,
      getReferencedColumn: (t) => t.invoiceId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoiceLinesTableFilterComposer(
            $db: $db,
            $table: $db.invoiceLines,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<bool> invoicePaymentsRefs(
    Expression<bool> Function($$InvoicePaymentsTableFilterComposer f) f,
  ) {
    final $$InvoicePaymentsTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.invoicePayments,
      getReferencedColumn: (t) => t.invoiceId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoicePaymentsTableFilterComposer(
            $db: $db,
            $table: $db.invoicePayments,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$InvoicesTableOrderingComposer
    extends Composer<_$AppDatabase, $InvoicesTable> {
  $$InvoicesTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get number => $composableBuilder(
    column: $table.number,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get issueDate => $composableBuilder(
    column: $table.issueDate,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get dueDate => $composableBuilder(
    column: $table.dueDate,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get periodFrom => $composableBuilder(
    column: $table.periodFrom,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get periodTo => $composableBuilder(
    column: $table.periodTo,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<int> get subtotal => $composableBuilder(
    column: $table.subtotal,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<double> get taxRate => $composableBuilder(
    column: $table.taxRate,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get taxLabel => $composableBuilder(
    column: $table.taxLabel,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<int> get taxAmount => $composableBuilder(
    column: $table.taxAmount,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<int> get total => $composableBuilder(
    column: $table.total,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get status => $composableBuilder(
    column: $table.status,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get notes => $composableBuilder(
    column: $table.notes,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnOrderings(column),
  );

  $$BranchesTableOrderingComposer get branchId {
    final $$BranchesTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.branchId,
      referencedTable: $db.branches,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$BranchesTableOrderingComposer(
            $db: $db,
            $table: $db.branches,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$CompaniesTableOrderingComposer get companyId {
    final $$CompaniesTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.companyId,
      referencedTable: $db.companies,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$CompaniesTableOrderingComposer(
            $db: $db,
            $table: $db.companies,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$SitesTableOrderingComposer get siteId {
    final $$SitesTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.siteId,
      referencedTable: $db.sites,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SitesTableOrderingComposer(
            $db: $db,
            $table: $db.sites,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$InvoicesTableAnnotationComposer
    extends Composer<_$AppDatabase, $InvoicesTable> {
  $$InvoicesTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get number =>
      $composableBuilder(column: $table.number, builder: (column) => column);

  GeneratedColumn<String> get issueDate =>
      $composableBuilder(column: $table.issueDate, builder: (column) => column);

  GeneratedColumn<String> get dueDate =>
      $composableBuilder(column: $table.dueDate, builder: (column) => column);

  GeneratedColumn<String> get periodFrom => $composableBuilder(
    column: $table.periodFrom,
    builder: (column) => column,
  );

  GeneratedColumn<String> get periodTo =>
      $composableBuilder(column: $table.periodTo, builder: (column) => column);

  GeneratedColumn<int> get subtotal =>
      $composableBuilder(column: $table.subtotal, builder: (column) => column);

  GeneratedColumn<double> get taxRate =>
      $composableBuilder(column: $table.taxRate, builder: (column) => column);

  GeneratedColumn<String> get taxLabel =>
      $composableBuilder(column: $table.taxLabel, builder: (column) => column);

  GeneratedColumn<int> get taxAmount =>
      $composableBuilder(column: $table.taxAmount, builder: (column) => column);

  GeneratedColumn<int> get total =>
      $composableBuilder(column: $table.total, builder: (column) => column);

  GeneratedColumn<String> get status =>
      $composableBuilder(column: $table.status, builder: (column) => column);

  GeneratedColumn<String> get notes =>
      $composableBuilder(column: $table.notes, builder: (column) => column);

  GeneratedColumn<DateTime> get createdAt =>
      $composableBuilder(column: $table.createdAt, builder: (column) => column);

  $$BranchesTableAnnotationComposer get branchId {
    final $$BranchesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.branchId,
      referencedTable: $db.branches,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$BranchesTableAnnotationComposer(
            $db: $db,
            $table: $db.branches,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$CompaniesTableAnnotationComposer get companyId {
    final $$CompaniesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.companyId,
      referencedTable: $db.companies,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$CompaniesTableAnnotationComposer(
            $db: $db,
            $table: $db.companies,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$SitesTableAnnotationComposer get siteId {
    final $$SitesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.siteId,
      referencedTable: $db.sites,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SitesTableAnnotationComposer(
            $db: $db,
            $table: $db.sites,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  Expression<T> invoiceLinesRefs<T extends Object>(
    Expression<T> Function($$InvoiceLinesTableAnnotationComposer a) f,
  ) {
    final $$InvoiceLinesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.invoiceLines,
      getReferencedColumn: (t) => t.invoiceId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoiceLinesTableAnnotationComposer(
            $db: $db,
            $table: $db.invoiceLines,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }

  Expression<T> invoicePaymentsRefs<T extends Object>(
    Expression<T> Function($$InvoicePaymentsTableAnnotationComposer a) f,
  ) {
    final $$InvoicePaymentsTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.invoicePayments,
      getReferencedColumn: (t) => t.invoiceId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoicePaymentsTableAnnotationComposer(
            $db: $db,
            $table: $db.invoicePayments,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$InvoicesTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $InvoicesTable,
          Invoice,
          $$InvoicesTableFilterComposer,
          $$InvoicesTableOrderingComposer,
          $$InvoicesTableAnnotationComposer,
          $$InvoicesTableCreateCompanionBuilder,
          $$InvoicesTableUpdateCompanionBuilder,
          (Invoice, $$InvoicesTableReferences),
          Invoice,
          PrefetchHooks Function({
            bool branchId,
            bool companyId,
            bool siteId,
            bool invoiceLinesRefs,
            bool invoicePaymentsRefs,
          })
        > {
  $$InvoicesTableTableManager(_$AppDatabase db, $InvoicesTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$InvoicesTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$InvoicesTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$InvoicesTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> branchId = const Value.absent(),
                Value<String> companyId = const Value.absent(),
                Value<String?> siteId = const Value.absent(),
                Value<String> number = const Value.absent(),
                Value<String> issueDate = const Value.absent(),
                Value<String> dueDate = const Value.absent(),
                Value<String> periodFrom = const Value.absent(),
                Value<String> periodTo = const Value.absent(),
                Value<int> subtotal = const Value.absent(),
                Value<double> taxRate = const Value.absent(),
                Value<String> taxLabel = const Value.absent(),
                Value<int> taxAmount = const Value.absent(),
                Value<int> total = const Value.absent(),
                Value<String> status = const Value.absent(),
                Value<String> notes = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => InvoicesCompanion(
                id: id,
                branchId: branchId,
                companyId: companyId,
                siteId: siteId,
                number: number,
                issueDate: issueDate,
                dueDate: dueDate,
                periodFrom: periodFrom,
                periodTo: periodTo,
                subtotal: subtotal,
                taxRate: taxRate,
                taxLabel: taxLabel,
                taxAmount: taxAmount,
                total: total,
                status: status,
                notes: notes,
                createdAt: createdAt,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String branchId,
                required String companyId,
                Value<String?> siteId = const Value.absent(),
                required String number,
                required String issueDate,
                required String dueDate,
                required String periodFrom,
                required String periodTo,
                required int subtotal,
                Value<double> taxRate = const Value.absent(),
                Value<String> taxLabel = const Value.absent(),
                Value<int> taxAmount = const Value.absent(),
                required int total,
                Value<String> status = const Value.absent(),
                Value<String> notes = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => InvoicesCompanion.insert(
                id: id,
                branchId: branchId,
                companyId: companyId,
                siteId: siteId,
                number: number,
                issueDate: issueDate,
                dueDate: dueDate,
                periodFrom: periodFrom,
                periodTo: periodTo,
                subtotal: subtotal,
                taxRate: taxRate,
                taxLabel: taxLabel,
                taxAmount: taxAmount,
                total: total,
                status: status,
                notes: notes,
                createdAt: createdAt,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$InvoicesTable, Invoice>(table),
                  $$InvoicesTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback:
              ({
                branchId = false,
                companyId = false,
                siteId = false,
                invoiceLinesRefs = false,
                invoicePaymentsRefs = false,
              }) {
                return PrefetchHooks(
                  db: db,
                  explicitlyWatchedTables: [
                    if (invoiceLinesRefs) db.invoiceLines,
                    if (invoicePaymentsRefs) db.invoicePayments,
                  ],
                  addJoins:
                      <
                        T extends TableManagerState<
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic,
                          dynamic
                        >
                      >(state) {
                        if (branchId) {
                          state = state.withJoin(
                            currentTable: table,
                            currentColumn: table.branchId,
                            referencedTable: $$InvoicesTableReferences
                                ._branchIdTable(db),
                            referencedColumn: $$InvoicesTableReferences
                                ._branchIdTable(db)
                                .id,
                          ) as T;
                        }
                        if (companyId) {
                          state = state.withJoin(
                            currentTable: table,
                            currentColumn: table.companyId,
                            referencedTable: $$InvoicesTableReferences
                                ._companyIdTable(db),
                            referencedColumn: $$InvoicesTableReferences
                                ._companyIdTable(db)
                                .id,
                          ) as T;
                        }
                        if (siteId) {
                          state = state.withJoin(
                            currentTable: table,
                            currentColumn: table.siteId,
                            referencedTable: $$InvoicesTableReferences
                                ._siteIdTable(db),
                            referencedColumn: $$InvoicesTableReferences
                                ._siteIdTable(db)
                                .id,
                          ) as T;
                        }

                        return state;
                      },
                  getPrefetchedDataCallback: (items) async {
                    return [
                      if (invoiceLinesRefs)
                        await $_getPrefetchedData<
                          Invoice,
                          $InvoicesTable,
                          InvoiceLine
                        >(
                          currentTable: table,
                          referencedTable: $$InvoicesTableReferences
                              ._invoiceLinesRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$InvoicesTableReferences(
                                db,
                                table,
                                p0,
                              ).invoiceLinesRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.invoiceId == item.id,
                              ),
                          typedResults: items,
                        ),
                      if (invoicePaymentsRefs)
                        await $_getPrefetchedData<
                          Invoice,
                          $InvoicesTable,
                          InvoicePayment
                        >(
                          currentTable: table,
                          referencedTable: $$InvoicesTableReferences
                              ._invoicePaymentsRefsTable(db),
                          managerFromTypedResult: (p0) =>
                              $$InvoicesTableReferences(
                                db,
                                table,
                                p0,
                              ).invoicePaymentsRefs,
                          referencedItemsForCurrentItem:
                              (item, referencedItems) => referencedItems.where(
                                (e) => e.invoiceId == item.id,
                              ),
                          typedResults: items,
                        ),
                    ];
                  },
                );
              },
        ),
      );
}

typedef $$InvoicesTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $InvoicesTable,
      Invoice,
      $$InvoicesTableFilterComposer,
      $$InvoicesTableOrderingComposer,
      $$InvoicesTableAnnotationComposer,
      $$InvoicesTableCreateCompanionBuilder,
      $$InvoicesTableUpdateCompanionBuilder,
      (Invoice, $$InvoicesTableReferences),
      Invoice,
      PrefetchHooks Function({
        bool branchId,
        bool companyId,
        bool siteId,
        bool invoiceLinesRefs,
        bool invoicePaymentsRefs,
      })
    >;
typedef $$InvoiceLinesTableCreateCompanionBuilder =
    InvoiceLinesCompanion Function({
      required String id,
      required String invoiceId,
      Value<String?> labourId,
      required String description,
      Value<String> skill,
      required double days,
      required int rate,
      Value<double> otHours,
      Value<int> otRate,
      required int amount,
      Value<int> rowid,
    });
typedef $$InvoiceLinesTableUpdateCompanionBuilder =
    InvoiceLinesCompanion Function({
      Value<String> id,
      Value<String> invoiceId,
      Value<String?> labourId,
      Value<String> description,
      Value<String> skill,
      Value<double> days,
      Value<int> rate,
      Value<double> otHours,
      Value<int> otRate,
      Value<int> amount,
      Value<int> rowid,
    });

final class $$InvoiceLinesTableReferences
    extends BaseReferences<_$AppDatabase, $InvoiceLinesTable, InvoiceLine> {
  $$InvoiceLinesTableReferences(super.$_db, super.$_table, super.$_typedResult);

  static $InvoicesTable _invoiceIdTable(_$AppDatabase db) =>
      db.invoices.createAlias('invoice_lines__invoice_id__invoices__id');

  $$InvoicesTableProcessedTableManager get invoiceId {
    final $_column = $_itemColumn<String>('invoice_id')!;

    final manager = $$InvoicesTableTableManager(
      $_db,
      $_db.invoices,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_invoiceIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }

  static $LaboursTable _labourIdTable(_$AppDatabase db) =>
      db.labours.createAlias('invoice_lines__labour_id__labours__id');

  $$LaboursTableProcessedTableManager? get labourId {
    final $_column = $_itemColumn<String>('labour_id');
    if ($_column == null) return null;
    final manager = $$LaboursTableTableManager(
      $_db,
      $_db.labours,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_labourIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }
}

class $$InvoiceLinesTableFilterComposer
    extends Composer<_$AppDatabase, $InvoiceLinesTable> {
  $$InvoiceLinesTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get description => $composableBuilder(
    column: $table.description,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get skill => $composableBuilder(
    column: $table.skill,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<double> get days => $composableBuilder(
    column: $table.days,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<int> get rate => $composableBuilder(
    column: $table.rate,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<double> get otHours => $composableBuilder(
    column: $table.otHours,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<int> get otRate => $composableBuilder(
    column: $table.otRate,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<int> get amount => $composableBuilder(
    column: $table.amount,
    builder: (column) => ColumnFilters(column),
  );

  $$InvoicesTableFilterComposer get invoiceId {
    final $$InvoicesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.invoiceId,
      referencedTable: $db.invoices,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoicesTableFilterComposer(
            $db: $db,
            $table: $db.invoices,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$LaboursTableFilterComposer get labourId {
    final $$LaboursTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableFilterComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$InvoiceLinesTableOrderingComposer
    extends Composer<_$AppDatabase, $InvoiceLinesTable> {
  $$InvoiceLinesTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get description => $composableBuilder(
    column: $table.description,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get skill => $composableBuilder(
    column: $table.skill,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<double> get days => $composableBuilder(
    column: $table.days,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<int> get rate => $composableBuilder(
    column: $table.rate,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<double> get otHours => $composableBuilder(
    column: $table.otHours,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<int> get otRate => $composableBuilder(
    column: $table.otRate,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<int> get amount => $composableBuilder(
    column: $table.amount,
    builder: (column) => ColumnOrderings(column),
  );

  $$InvoicesTableOrderingComposer get invoiceId {
    final $$InvoicesTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.invoiceId,
      referencedTable: $db.invoices,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoicesTableOrderingComposer(
            $db: $db,
            $table: $db.invoices,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$LaboursTableOrderingComposer get labourId {
    final $$LaboursTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableOrderingComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$InvoiceLinesTableAnnotationComposer
    extends Composer<_$AppDatabase, $InvoiceLinesTable> {
  $$InvoiceLinesTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get description => $composableBuilder(
    column: $table.description,
    builder: (column) => column,
  );

  GeneratedColumn<String> get skill =>
      $composableBuilder(column: $table.skill, builder: (column) => column);

  GeneratedColumn<double> get days =>
      $composableBuilder(column: $table.days, builder: (column) => column);

  GeneratedColumn<int> get rate =>
      $composableBuilder(column: $table.rate, builder: (column) => column);

  GeneratedColumn<double> get otHours =>
      $composableBuilder(column: $table.otHours, builder: (column) => column);

  GeneratedColumn<int> get otRate =>
      $composableBuilder(column: $table.otRate, builder: (column) => column);

  GeneratedColumn<int> get amount =>
      $composableBuilder(column: $table.amount, builder: (column) => column);

  $$InvoicesTableAnnotationComposer get invoiceId {
    final $$InvoicesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.invoiceId,
      referencedTable: $db.invoices,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoicesTableAnnotationComposer(
            $db: $db,
            $table: $db.invoices,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }

  $$LaboursTableAnnotationComposer get labourId {
    final $$LaboursTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.labourId,
      referencedTable: $db.labours,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$LaboursTableAnnotationComposer(
            $db: $db,
            $table: $db.labours,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$InvoiceLinesTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $InvoiceLinesTable,
          InvoiceLine,
          $$InvoiceLinesTableFilterComposer,
          $$InvoiceLinesTableOrderingComposer,
          $$InvoiceLinesTableAnnotationComposer,
          $$InvoiceLinesTableCreateCompanionBuilder,
          $$InvoiceLinesTableUpdateCompanionBuilder,
          (InvoiceLine, $$InvoiceLinesTableReferences),
          InvoiceLine,
          PrefetchHooks Function({bool invoiceId, bool labourId})
        > {
  $$InvoiceLinesTableTableManager(_$AppDatabase db, $InvoiceLinesTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$InvoiceLinesTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$InvoiceLinesTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$InvoiceLinesTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> invoiceId = const Value.absent(),
                Value<String?> labourId = const Value.absent(),
                Value<String> description = const Value.absent(),
                Value<String> skill = const Value.absent(),
                Value<double> days = const Value.absent(),
                Value<int> rate = const Value.absent(),
                Value<double> otHours = const Value.absent(),
                Value<int> otRate = const Value.absent(),
                Value<int> amount = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => InvoiceLinesCompanion(
                id: id,
                invoiceId: invoiceId,
                labourId: labourId,
                description: description,
                skill: skill,
                days: days,
                rate: rate,
                otHours: otHours,
                otRate: otRate,
                amount: amount,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String invoiceId,
                Value<String?> labourId = const Value.absent(),
                required String description,
                Value<String> skill = const Value.absent(),
                required double days,
                required int rate,
                Value<double> otHours = const Value.absent(),
                Value<int> otRate = const Value.absent(),
                required int amount,
                Value<int> rowid = const Value.absent(),
              }) => InvoiceLinesCompanion.insert(
                id: id,
                invoiceId: invoiceId,
                labourId: labourId,
                description: description,
                skill: skill,
                days: days,
                rate: rate,
                otHours: otHours,
                otRate: otRate,
                amount: amount,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$InvoiceLinesTable, InvoiceLine>(table),
                  $$InvoiceLinesTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback: ({invoiceId = false, labourId = false}) {
            return PrefetchHooks(
              db: db,
              explicitlyWatchedTables: [],
              addJoins:
                  <
                    T extends TableManagerState<
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic
                    >
                  >(state) {
                    if (invoiceId) {
                      state = state.withJoin(
                        currentTable: table,
                        currentColumn: table.invoiceId,
                        referencedTable: $$InvoiceLinesTableReferences
                            ._invoiceIdTable(db),
                        referencedColumn: $$InvoiceLinesTableReferences
                            ._invoiceIdTable(db)
                            .id,
                      ) as T;
                    }
                    if (labourId) {
                      state = state.withJoin(
                        currentTable: table,
                        currentColumn: table.labourId,
                        referencedTable: $$InvoiceLinesTableReferences
                            ._labourIdTable(db),
                        referencedColumn: $$InvoiceLinesTableReferences
                            ._labourIdTable(db)
                            .id,
                      ) as T;
                    }

                    return state;
                  },
              getPrefetchedDataCallback: (items) async {
                return [];
              },
            );
          },
        ),
      );
}

typedef $$InvoiceLinesTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $InvoiceLinesTable,
      InvoiceLine,
      $$InvoiceLinesTableFilterComposer,
      $$InvoiceLinesTableOrderingComposer,
      $$InvoiceLinesTableAnnotationComposer,
      $$InvoiceLinesTableCreateCompanionBuilder,
      $$InvoiceLinesTableUpdateCompanionBuilder,
      (InvoiceLine, $$InvoiceLinesTableReferences),
      InvoiceLine,
      PrefetchHooks Function({bool invoiceId, bool labourId})
    >;
typedef $$InvoicePaymentsTableCreateCompanionBuilder =
    InvoicePaymentsCompanion Function({
      required String id,
      required String invoiceId,
      required int amount,
      required String date,
      Value<String> mode,
      Value<String> reference,
      Value<String> note,
      Value<DateTime> createdAt,
      Value<int> rowid,
    });
typedef $$InvoicePaymentsTableUpdateCompanionBuilder =
    InvoicePaymentsCompanion Function({
      Value<String> id,
      Value<String> invoiceId,
      Value<int> amount,
      Value<String> date,
      Value<String> mode,
      Value<String> reference,
      Value<String> note,
      Value<DateTime> createdAt,
      Value<int> rowid,
    });

final class $$InvoicePaymentsTableReferences
    extends
        BaseReferences<_$AppDatabase, $InvoicePaymentsTable, InvoicePayment> {
  $$InvoicePaymentsTableReferences(
    super.$_db,
    super.$_table,
    super.$_typedResult,
  );

  static $InvoicesTable _invoiceIdTable(_$AppDatabase db) =>
      db.invoices.createAlias('invoice_payments__invoice_id__invoices__id');

  $$InvoicesTableProcessedTableManager get invoiceId {
    final $_column = $_itemColumn<String>('invoice_id')!;

    final manager = $$InvoicesTableTableManager(
      $_db,
      $_db.invoices,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_invoiceIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }
}

class $$InvoicePaymentsTableFilterComposer
    extends Composer<_$AppDatabase, $InvoicePaymentsTable> {
  $$InvoicePaymentsTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<int> get amount => $composableBuilder(
    column: $table.amount,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get date => $composableBuilder(
    column: $table.date,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get mode => $composableBuilder(
    column: $table.mode,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get reference => $composableBuilder(
    column: $table.reference,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get note => $composableBuilder(
    column: $table.note,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnFilters(column),
  );

  $$InvoicesTableFilterComposer get invoiceId {
    final $$InvoicesTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.invoiceId,
      referencedTable: $db.invoices,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoicesTableFilterComposer(
            $db: $db,
            $table: $db.invoices,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$InvoicePaymentsTableOrderingComposer
    extends Composer<_$AppDatabase, $InvoicePaymentsTable> {
  $$InvoicePaymentsTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<int> get amount => $composableBuilder(
    column: $table.amount,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get date => $composableBuilder(
    column: $table.date,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get mode => $composableBuilder(
    column: $table.mode,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get reference => $composableBuilder(
    column: $table.reference,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get note => $composableBuilder(
    column: $table.note,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnOrderings(column),
  );

  $$InvoicesTableOrderingComposer get invoiceId {
    final $$InvoicesTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.invoiceId,
      referencedTable: $db.invoices,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoicesTableOrderingComposer(
            $db: $db,
            $table: $db.invoices,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$InvoicePaymentsTableAnnotationComposer
    extends Composer<_$AppDatabase, $InvoicePaymentsTable> {
  $$InvoicePaymentsTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<int> get amount =>
      $composableBuilder(column: $table.amount, builder: (column) => column);

  GeneratedColumn<String> get date =>
      $composableBuilder(column: $table.date, builder: (column) => column);

  GeneratedColumn<String> get mode =>
      $composableBuilder(column: $table.mode, builder: (column) => column);

  GeneratedColumn<String> get reference =>
      $composableBuilder(column: $table.reference, builder: (column) => column);

  GeneratedColumn<String> get note =>
      $composableBuilder(column: $table.note, builder: (column) => column);

  GeneratedColumn<DateTime> get createdAt =>
      $composableBuilder(column: $table.createdAt, builder: (column) => column);

  $$InvoicesTableAnnotationComposer get invoiceId {
    final $$InvoicesTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.invoiceId,
      referencedTable: $db.invoices,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$InvoicesTableAnnotationComposer(
            $db: $db,
            $table: $db.invoices,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$InvoicePaymentsTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $InvoicePaymentsTable,
          InvoicePayment,
          $$InvoicePaymentsTableFilterComposer,
          $$InvoicePaymentsTableOrderingComposer,
          $$InvoicePaymentsTableAnnotationComposer,
          $$InvoicePaymentsTableCreateCompanionBuilder,
          $$InvoicePaymentsTableUpdateCompanionBuilder,
          (InvoicePayment, $$InvoicePaymentsTableReferences),
          InvoicePayment,
          PrefetchHooks Function({bool invoiceId})
        > {
  $$InvoicePaymentsTableTableManager(
    _$AppDatabase db,
    $InvoicePaymentsTable table,
  ) : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$InvoicePaymentsTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$InvoicePaymentsTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$InvoicePaymentsTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<String> invoiceId = const Value.absent(),
                Value<int> amount = const Value.absent(),
                Value<String> date = const Value.absent(),
                Value<String> mode = const Value.absent(),
                Value<String> reference = const Value.absent(),
                Value<String> note = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => InvoicePaymentsCompanion(
                id: id,
                invoiceId: invoiceId,
                amount: amount,
                date: date,
                mode: mode,
                reference: reference,
                note: note,
                createdAt: createdAt,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                required String invoiceId,
                required int amount,
                required String date,
                Value<String> mode = const Value.absent(),
                Value<String> reference = const Value.absent(),
                Value<String> note = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => InvoicePaymentsCompanion.insert(
                id: id,
                invoiceId: invoiceId,
                amount: amount,
                date: date,
                mode: mode,
                reference: reference,
                note: note,
                createdAt: createdAt,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$InvoicePaymentsTable, InvoicePayment>(table),
                  $$InvoicePaymentsTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback: ({invoiceId = false}) {
            return PrefetchHooks(
              db: db,
              explicitlyWatchedTables: [],
              addJoins:
                  <
                    T extends TableManagerState<
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic
                    >
                  >(state) {
                    if (invoiceId) {
                      state = state.withJoin(
                        currentTable: table,
                        currentColumn: table.invoiceId,
                        referencedTable: $$InvoicePaymentsTableReferences
                            ._invoiceIdTable(db),
                        referencedColumn: $$InvoicePaymentsTableReferences
                            ._invoiceIdTable(db)
                            .id,
                      ) as T;
                    }

                    return state;
                  },
              getPrefetchedDataCallback: (items) async {
                return [];
              },
            );
          },
        ),
      );
}

typedef $$InvoicePaymentsTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $InvoicePaymentsTable,
      InvoicePayment,
      $$InvoicePaymentsTableFilterComposer,
      $$InvoicePaymentsTableOrderingComposer,
      $$InvoicePaymentsTableAnnotationComposer,
      $$InvoicePaymentsTableCreateCompanionBuilder,
      $$InvoicePaymentsTableUpdateCompanionBuilder,
      (InvoicePayment, $$InvoicePaymentsTableReferences),
      InvoicePayment,
      PrefetchHooks Function({bool invoiceId})
    >;
typedef $$AuditLogsTableCreateCompanionBuilder = AuditLogsCompanion Function({
  required String id,
  Value<DateTime> at,
  Value<String?> staffId,
  required String action,
  required String entity,
  Value<String> entityId,
  Value<String> detail,
  Value<int> rowid,
});
typedef $$AuditLogsTableUpdateCompanionBuilder = AuditLogsCompanion Function({
  Value<String> id,
  Value<DateTime> at,
  Value<String?> staffId,
  Value<String> action,
  Value<String> entity,
  Value<String> entityId,
  Value<String> detail,
  Value<int> rowid,
});

class $$AuditLogsTableFilterComposer
    extends Composer<_$AppDatabase, $AuditLogsTable> {
  $$AuditLogsTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get at => $composableBuilder(
    column: $table.at,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get staffId => $composableBuilder(
    column: $table.staffId,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get action => $composableBuilder(
    column: $table.action,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get entity => $composableBuilder(
    column: $table.entity,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get entityId => $composableBuilder(
    column: $table.entityId,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get detail => $composableBuilder(
    column: $table.detail,
    builder: (column) => ColumnFilters(column),
  );
}

class $$AuditLogsTableOrderingComposer
    extends Composer<_$AppDatabase, $AuditLogsTable> {
  $$AuditLogsTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get at => $composableBuilder(
    column: $table.at,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get staffId => $composableBuilder(
    column: $table.staffId,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get action => $composableBuilder(
    column: $table.action,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get entity => $composableBuilder(
    column: $table.entity,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get entityId => $composableBuilder(
    column: $table.entityId,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get detail => $composableBuilder(
    column: $table.detail,
    builder: (column) => ColumnOrderings(column),
  );
}

class $$AuditLogsTableAnnotationComposer
    extends Composer<_$AppDatabase, $AuditLogsTable> {
  $$AuditLogsTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<DateTime> get at =>
      $composableBuilder(column: $table.at, builder: (column) => column);

  GeneratedColumn<String> get staffId =>
      $composableBuilder(column: $table.staffId, builder: (column) => column);

  GeneratedColumn<String> get action =>
      $composableBuilder(column: $table.action, builder: (column) => column);

  GeneratedColumn<String> get entity =>
      $composableBuilder(column: $table.entity, builder: (column) => column);

  GeneratedColumn<String> get entityId =>
      $composableBuilder(column: $table.entityId, builder: (column) => column);

  GeneratedColumn<String> get detail =>
      $composableBuilder(column: $table.detail, builder: (column) => column);
}

class $$AuditLogsTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $AuditLogsTable,
          AuditLog,
          $$AuditLogsTableFilterComposer,
          $$AuditLogsTableOrderingComposer,
          $$AuditLogsTableAnnotationComposer,
          $$AuditLogsTableCreateCompanionBuilder,
          $$AuditLogsTableUpdateCompanionBuilder,
          (AuditLog, BaseReferences<_$AppDatabase, $AuditLogsTable, AuditLog>),
          AuditLog,
          PrefetchHooks Function()
        > {
  $$AuditLogsTableTableManager(_$AppDatabase db, $AuditLogsTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$AuditLogsTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$AuditLogsTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$AuditLogsTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> id = const Value.absent(),
                Value<DateTime> at = const Value.absent(),
                Value<String?> staffId = const Value.absent(),
                Value<String> action = const Value.absent(),
                Value<String> entity = const Value.absent(),
                Value<String> entityId = const Value.absent(),
                Value<String> detail = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => AuditLogsCompanion(
                id: id,
                at: at,
                staffId: staffId,
                action: action,
                entity: entity,
                entityId: entityId,
                detail: detail,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String id,
                Value<DateTime> at = const Value.absent(),
                Value<String?> staffId = const Value.absent(),
                required String action,
                required String entity,
                Value<String> entityId = const Value.absent(),
                Value<String> detail = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => AuditLogsCompanion.insert(
                id: id,
                at: at,
                staffId: staffId,
                action: action,
                entity: entity,
                entityId: entityId,
                detail: detail,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$AuditLogsTable, AuditLog>(table),
                  BaseReferences<_$AppDatabase, $AuditLogsTable, AuditLog>(
                    db,
                    table,
                    e,
                  ),
                ),
              )
              .toList(),
          prefetchHooksCallback: null,
        ),
      );
}

typedef $$AuditLogsTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $AuditLogsTable,
      AuditLog,
      $$AuditLogsTableFilterComposer,
      $$AuditLogsTableOrderingComposer,
      $$AuditLogsTableAnnotationComposer,
      $$AuditLogsTableCreateCompanionBuilder,
      $$AuditLogsTableUpdateCompanionBuilder,
      (AuditLog, BaseReferences<_$AppDatabase, $AuditLogsTable, AuditLog>),
      AuditLog,
      PrefetchHooks Function()
    >;
typedef $$KeyValuesTableCreateCompanionBuilder = KeyValuesCompanion Function({
  required String key,
  required String value,
  Value<int> rowid,
});
typedef $$KeyValuesTableUpdateCompanionBuilder = KeyValuesCompanion Function({
  Value<String> key,
  Value<String> value,
  Value<int> rowid,
});

class $$KeyValuesTableFilterComposer
    extends Composer<_$AppDatabase, $KeyValuesTable> {
  $$KeyValuesTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get key => $composableBuilder(
    column: $table.key,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get value => $composableBuilder(
    column: $table.value,
    builder: (column) => ColumnFilters(column),
  );
}

class $$KeyValuesTableOrderingComposer
    extends Composer<_$AppDatabase, $KeyValuesTable> {
  $$KeyValuesTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get key => $composableBuilder(
    column: $table.key,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get value => $composableBuilder(
    column: $table.value,
    builder: (column) => ColumnOrderings(column),
  );
}

class $$KeyValuesTableAnnotationComposer
    extends Composer<_$AppDatabase, $KeyValuesTable> {
  $$KeyValuesTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get key =>
      $composableBuilder(column: $table.key, builder: (column) => column);

  GeneratedColumn<String> get value =>
      $composableBuilder(column: $table.value, builder: (column) => column);
}

class $$KeyValuesTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $KeyValuesTable,
          KeyValue,
          $$KeyValuesTableFilterComposer,
          $$KeyValuesTableOrderingComposer,
          $$KeyValuesTableAnnotationComposer,
          $$KeyValuesTableCreateCompanionBuilder,
          $$KeyValuesTableUpdateCompanionBuilder,
          (KeyValue, BaseReferences<_$AppDatabase, $KeyValuesTable, KeyValue>),
          KeyValue,
          PrefetchHooks Function()
        > {
  $$KeyValuesTableTableManager(_$AppDatabase db, $KeyValuesTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$KeyValuesTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$KeyValuesTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$KeyValuesTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback: ({
            Value<String> key = const Value.absent(),
            Value<String> value = const Value.absent(),
            Value<int> rowid = const Value.absent(),
          }) => KeyValuesCompanion(key: key, value: value, rowid: rowid),
          createCompanionCallback: ({
            required String key,
            required String value,
            Value<int> rowid = const Value.absent(),
          }) => KeyValuesCompanion.insert(key: key, value: value, rowid: rowid),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable<$KeyValuesTable, KeyValue>(table),
                  BaseReferences<_$AppDatabase, $KeyValuesTable, KeyValue>(
                    db,
                    table,
                    e,
                  ),
                ),
              )
              .toList(),
          prefetchHooksCallback: null,
        ),
      );
}

typedef $$KeyValuesTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $KeyValuesTable,
      KeyValue,
      $$KeyValuesTableFilterComposer,
      $$KeyValuesTableOrderingComposer,
      $$KeyValuesTableAnnotationComposer,
      $$KeyValuesTableCreateCompanionBuilder,
      $$KeyValuesTableUpdateCompanionBuilder,
      (KeyValue, BaseReferences<_$AppDatabase, $KeyValuesTable, KeyValue>),
      KeyValue,
      PrefetchHooks Function()
    >;

class $AppDatabaseManager {
  final _$AppDatabase _db;
  $AppDatabaseManager(this._db);
  $$BranchesTableTableManager get branches =>
      $$BranchesTableTableManager(_db, _db.branches);
  $$RolesTableTableManager get roles =>
      $$RolesTableTableManager(_db, _db.roles);
  $$StaffMembersTableTableManager get staffMembers =>
      $$StaffMembersTableTableManager(_db, _db.staffMembers);
  $$CompaniesTableTableManager get companies =>
      $$CompaniesTableTableManager(_db, _db.companies);
  $$SitesTableTableManager get sites =>
      $$SitesTableTableManager(_db, _db.sites);
  $$ContractsTableTableManager get contracts =>
      $$ContractsTableTableManager(_db, _db.contracts);
  $$ContractRatesTableTableManager get contractRates =>
      $$ContractRatesTableTableManager(_db, _db.contractRates);
  $$LaboursTableTableManager get labours =>
      $$LaboursTableTableManager(_db, _db.labours);
  $$LabourRatesTableTableManager get labourRates =>
      $$LabourRatesTableTableManager(_db, _db.labourRates);
  $$LabourDocumentsTableTableManager get labourDocuments =>
      $$LabourDocumentsTableTableManager(_db, _db.labourDocuments);
  $$AssignmentsTableTableManager get assignments =>
      $$AssignmentsTableTableManager(_db, _db.assignments);
  $$AttendanceTableTableManager get attendance =>
      $$AttendanceTableTableManager(_db, _db.attendance);
  $$AttendanceSheetsTableTableManager get attendanceSheets =>
      $$AttendanceSheetsTableTableManager(_db, _db.attendanceSheets);
  $$PaymentsTableTableManager get payments =>
      $$PaymentsTableTableManager(_db, _db.payments);
  $$InvoicesTableTableManager get invoices =>
      $$InvoicesTableTableManager(_db, _db.invoices);
  $$InvoiceLinesTableTableManager get invoiceLines =>
      $$InvoiceLinesTableTableManager(_db, _db.invoiceLines);
  $$InvoicePaymentsTableTableManager get invoicePayments =>
      $$InvoicePaymentsTableTableManager(_db, _db.invoicePayments);
  $$AuditLogsTableTableManager get auditLogs =>
      $$AuditLogsTableTableManager(_db, _db.auditLogs);
  $$KeyValuesTableTableManager get keyValues =>
      $$KeyValuesTableTableManager(_db, _db.keyValues);
}
