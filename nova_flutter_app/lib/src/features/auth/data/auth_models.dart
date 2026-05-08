class NovaUser {
  const NovaUser({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
  });

  final int id;
  final String name;
  final String email;
  final String? phone;

  factory NovaUser.fromJson(Map<String, dynamic> json) {
    return NovaUser(
      id: (json['id'] as num).toInt(),
      name: '${json['name'] ?? ''}',
      email: '${json['email'] ?? ''}',
      phone: json['phone'] as String?,
    );
  }
}
