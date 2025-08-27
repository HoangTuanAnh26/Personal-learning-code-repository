// import java.util.Scanner;

// public class PersonInfo {

// public static void main(String[] args) {
// askPersonInfo();
// }

// public static boolean askYesNo(String prompt) {
// Scanner scanner = new Scanner(System.in);
// while (true) {
// System.out.print(prompt);
// String answer = scanner.nextLine().toLowerCase();
// if (answer.equals("yes")) {
// return true;
// } else if (answer.equals("no")) {
// return false;
// }
// }
// }

// public static int calculateAge(int yearBorn) {
// int currentYear = java.time.Year.now().getValue();
// return currentYear - yearBorn;
// }

// public static double convertMeterToFeet(double meter) {
// final double METER_TO_FEET = 3.281;
// double feet = meter * METER_TO_FEET;
// return Math.round(feet * 10.0) / 10.0;
// }

// public static void printHeightInfo(double heightFeet, boolean isMale) {
// if (isMale) {
// if (heightFeet > 6.5) {
// System.out.print("You are ");
// for (int i = 0; i < 10; i++) {
// System.out.print("very ");
// }
// System.out.println("tall as a man");
// } else if (heightFeet > 6.0) {
// System.out.println("You are tall as a man");
// } else {
// System.out.println("You are short as a man");
// }
// } else {
// if (heightFeet > 5.7) {
// System.out.println("You are tall as a girl");
// } else if (heightFeet < 5.0) {
// System.out.print("You are ");
// for (int i = 0; i < 10; i++) {
// System.out.print("very ");
// }
// System.out.println("short as a girl");
// } else {
// System.out.println("You are short as a girl");
// }
// }
// }

// public static void printPersonInfo(String firstname, String lastname, int
// age, double heightFeet,
// boolean isVietnamese, boolean isMale) {
// int currentYear = java.time.Year.now().getValue();
// System.out.println("\n---");
// System.out.println("Your name is " + firstname + " " + lastname);
// System.out.printf("%s is %d years old in %d\n", firstname, age, currentYear);
// System.out.println("You are " + heightFeet + " feet tall");
// if (isVietnamese) {
// System.out.println("You are from Vietnam");
// } else {
// System.out.println("You are not from Vietnam");
// }
// printHeightInfo(heightFeet, isMale);
// }

// public static void askPersonInfo() {
// Scanner scanner = new Scanner(System.in);

// System.out.print("Your firstname: ");
// String firstname = scanner.nextLine();

// System.out.print("Your lastname: ");
// String lastname = scanner.nextLine();

// System.out.print("When you were born: ");
// int yearBorn = Integer.parseInt(scanner.nextLine());

// System.out.print("Your height (meter): ");
// double heightMeter = Double.parseDouble(scanner.nextLine());

// boolean isMale = askYesNo("Are you male (yes/no): ");
// boolean isVietnamese = askYesNo("Are you Vietnamese (yes/no): ");

// int age = calculateAge(yearBorn);
// double heightFeet = convertMeterToFeet(heightMeter);
// printPersonInfo(firstname, lastname, age, heightFeet, isVietnamese, isMale);
// }
// }

import java.util.Scanner;
import java.time.Year;

public class PersonInfo {
	public static void main(String[] args) {
		askPersonInfo();
	}

	public static boolean askYesNo(String prompt) {
		Scanner scanner = new Scanner(System.in);
		while (true) {
			System.out.print(prompt);
			String answer = scanner.nextLine().toLowerCase();
			if (answer.equals("yes")) {
				return true;
			} else if (answer.equals("no")) {
				return false;
			} else {
				System.out.println("Please answer 'yes' or 'no'.");
			}
		}
	}

	public static int calculateAge(int yearBorn) {
		int currentYear = java.time.Year.now().getValue();
		return currentYear - yearBorn;
	}

	public static double convertMeterToFeet(double meter) {
		final double METER_TO_FEET = 3.281;
		double feet = meter * METER_TO_FEET;
		return Math.round(feet * 10.0) / 10.0;
	}

	public static void printHeightInfo(double heightFeet, boolean isMale) {
		if (isMale) {
			if (heightFeet > 6.5) {
				System.out.print("You are ");
				for (int i = 0; i < 10; i++) {
					System.out.print("very ");
				}
				System.out.println("tall as a man");
			} else if (heightFeet > 6.0) {
				System.out.println("You are tall as a man");
			} else {
				System.out.println("You are short as a man");
			}
		} else {
			if (heightFeet > 5.7) {
				System.out.println("You are tall as a girl");
			} else if (heightFeet < 5.0) {
				System.out.print("You are ");
				for (int i = 0; i < 10; i++) {
					System.out.print("very ");
				}
				System.out.println("short as a girl");
			} else {
				System.out.println("You are short as a girl");
			}
		}
	}

	public static void printPersonInfo(String firstname, String lastname, int age, double heightFeet,
			boolean isVietnamese, boolean isMale) {
		int currentYear = java.time.Year.now().getValue();
		System.out.println("\n---");
		System.out.println("Your name is " + firstname + " " + lastname);
		System.out.printf("%s is %d years old in %d\n", firstname, age, currentYear);
		System.out.println("You are " + heightFeet + " feet tall");
		if (isVietnamese) {
			System.out.println("You are from Vietnam");
		} else {
			System.out.println("You are not from Vietnam");
		}
	}

	public static void askPersonInfo() {
		Scanner scanner = new Scanner(System.in);

		System.out.print("Your firstname: ");
		String firstname = scanner.nextLine();

		System.out.print("Your lastname: ");
		String lastname = scanner.nextLine();

		System.out.print("When you were born: ");
		int yearBorn = Integer.parseInt(scanner.nextLine());

		System.out.print("Your height (meter): ");
		double heightMeter = Double.parseDouble(scanner.nextLine());

		boolean isMale = askYesNo("Are you male (yes/no): ");
		boolean isVietnamese = askYesNo("Are you Vietnamese (yes/no): ");

		int age = calculateAge(yearBorn);
		double heightFeet = convertMeterToFeet(heightMeter);
		printPersonInfo(firstname, lastname, age, heightFeet, isVietnamese, isMale);
	}
}

// Van con loi nho