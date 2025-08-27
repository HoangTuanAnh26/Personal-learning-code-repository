// import java.util.Scanner;

// public class SalesCalculator {

// public static double calcTotalPrice(double appleWeight, double APPLE_PRICE) {
// return appleWeight * APPLE_PRICE;
// }

// public static double calcReturn(double totalPrice, double moneyGiven) {
// if (moneyGiven < totalPrice) {
// return -1;
// }
// return moneyGiven - totalPrice;
// }

// public static void printReturnInfo(double total) {
// int count_500 = (int) (total / 500);
// total -= 500 * count_500;
// if (count_500 != 0)
// System.out.println("500k : " + count_500);

// int count_200 = (int) (total / 200);
// total -= 200 * count_200;
// if (count_200 != 0)
// System.out.println("200k : " + count_200);

// int count_100 = (int) (total / 100);
// total -= 100 * count_100;
// if (count_100 != 0)
// System.out.println("100k : " + count_100);

// int count_50 = (int) (total / 50);
// total -= 50 * count_50;
// if (count_50 != 0)
// System.out.println("50k : " + count_50);

// int count_20 = (int) (total / 20);
// total -= 20 * count_20;
// if (count_20 != 0)
// System.out.println("20k : " + count_20);

// int count_10 = (int) (total / 10);
// total -= 10 * count_10;
// if (count_10 != 0)
// System.out.println("10k : " + count_10);

// int count_1 = (int) total;
// if (count_1 != 0)
// System.out.println("1k : " + count_1);
// }

// public static void main(String[] args) {
// Scanner scanner = new Scanner(System.in);
// final double APPLE_PRICE = 21.0; // k VND

// System.out.print("Enter weight of apples: ");
// double appleWeight = scanner.nextDouble();

// System.out.print("Total money customer gives you: ");
// double moneyGiven = scanner.nextDouble();

// double totalPrice = calcTotalPrice(appleWeight, APPLE_PRICE);
// double moneyReturn = calcReturn(totalPrice, moneyGiven);

// System.out.println("Total price: " + (int) totalPrice + "k VND");

// if (moneyReturn == -1) {
// System.out.println("Not enough cash");
// } else {
// System.out.println("You need to return to customer: " +
// Math.round(moneyReturn * 100.0) / 100.0 + "k VND");
// printReturnInfo(moneyReturn);
// }

// scanner.close();
// }
// }

import java.util.Scanner;

public class SalesCalculator {

    public static double calcTotalPrice(double appleWeight, double APPLE_PRICE) {
        return appleWeight * APPLE_PRICE;
    }

    public static double calcReturn(double totalPrice, double moneyGiven) {
        if (moneyGiven < totalPrice) {
            return -1;
        }
        return moneyGiven - totalPrice;
    }

    public static void printReturnInfo(double total) {
        int count_500 = (int) (total / 500);
        total -= 500 * count_500;
        if (count_500 != 0)
            System.out.println("500k : " + count_500);

        int count_200 = (int) (total / 200);
        total -= 200 * count_200;
        if (count_200 != 0)
            System.out.println("200k : " + count_200);

        int count_100 = (int) (total / 100);
        total -= 100 * count_100;
        if (count_100 != 0)
            System.out.println("100k : " + count_100);

        int count_50 = (int) (total / 50);
        total -= 50 * count_50;
        if (count_50 != 0)
            System.out.println("50k : " + count_50);

        int count_20 = (int) (total / 20);
        total -= 20 * count_20;
        if (count_20 != 0)
            System.out.println("20k : " + count_20);

        int count_10 = (int) (total / 10);
        total -= 10 * count_10;
        if (count_10 != 0)
            System.out.println("10k : " + count_10);

        int count_1 = (int) (total / 1);
        total -= 1 * count_1;
        if (count_1 != 0)
            System.out.println("1k : " + count_1);
    }

    public static void main(String[] args) {
        Scanner scanner = new Scanner(System.in);
        final double APPLE_PRICE = 21.0; // k VND

        System.out.print("Enter weight of apples: ");
        double appleWeight = scanner.nextDouble();

        System.out.print("Total money customer gives you: ");
        double moneyGiven = scanner.nextDouble();

        double totalPrice = calcTotalPrice(appleWeight, APPLE_PRICE);
        double moneyReturn = calcReturn(totalPrice, moneyGiven);

        System.out.println("Total price: " + (int) totalPrice + "k VND");

        if (moneyReturn == -1) {
            System.out.println("Not enough cash");
        } else {
            System.out.println("You need to return to customer: " +
                    Math.round(moneyReturn * 100.0) / 100.0 + "k VND");
            printReturnInfo(moneyReturn);
        }

    }

}