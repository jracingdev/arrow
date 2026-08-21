class WorkerModel {
  String id;
  String firstName;
  String lastName;
  String email;
  String phoneNumber;
  String profilePictureURL;
  String providerId;
  String salary;
  bool online;
  bool active;

  WorkerModel({
    this.id = '',
    this.firstName = '',
    this.lastName = '',
    this.email = '',
    this.phoneNumber = '',
    this.profilePictureURL = '',
    this.providerId = '',
    this.salary = '',
    this.online = false,
    this.active = true,
  });

  String fullName() => '$firstName $lastName'.trim();

  factory WorkerModel.fromJson(Map<String, dynamic> json) {
    return WorkerModel(
      id: json['id']?.toString() ?? json['userID']?.toString() ?? '',
      firstName: json['firstName']?.toString() ?? '',
      lastName: json['lastName']?.toString() ?? '',
      email: json['email']?.toString() ?? '',
      phoneNumber: json['phoneNumber']?.toString() ?? '',
      profilePictureURL: json['profilePictureURL']?.toString() ?? '',
      providerId: json['providerId']?.toString() ?? '',
      salary: json['salary']?.toString() ?? '',
      online: json['online'] == true,
      active: json['active'] != false,
    );
  }
}
