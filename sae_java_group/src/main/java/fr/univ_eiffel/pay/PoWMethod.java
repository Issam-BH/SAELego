package fr.univ_eiffel.pay;

import java.io.IOException;
import java.util.HexFormat;

import com.google.gson.Gson;

public class PoWMethod implements PayMethod {

    public static final Solver POW_SOLVER = new Solver("SHA-256");
    private final FactoryClient client;
    private Gson gson = new Gson();

    public record Challenge(String dataPrefix, String hashPrefix) {}
    public record ChallengeAnswer(String dataPrefix, String hashPrefix, String answer) {}

    public PoWMethod(FactoryClient client) {
        this.client = client;
    }

    // Fetches a new PoW challenge
    public Challenge fetchChallenge() throws IOException {
        Challenge challenge = gson.fromJson(client.billingChallenge(), Challenge.class);
        System.err.println("Received PoW challenge: " + challenge);
        return challenge;
    }

    // Solves the challenge
    public ChallengeAnswer solveChallenge(Challenge challenge) {
        var startTime = System.nanoTime();
        byte[] dataPrefix = HexFormat.of().parseHex(challenge.dataPrefix());
        byte[] hashPrefix = HexFormat.of().parseHex(challenge.hashPrefix());
        byte[] solved = POW_SOLVER.solve(dataPrefix, hashPrefix);
        System.err.println("Challenge solved in " + (System.nanoTime() - startTime)/1e9 + " seconds");
        return new ChallengeAnswer(challenge.dataPrefix(), challenge.hashPrefix(), HexFormat.of().formatHex(solved));
    }

    // Submits the solution
    public void submitAnswer(ChallengeAnswer solution) throws IOException {
        client.billingChallengeAnswer(solution.dataPrefix(), solution.hashPrefix(), solution.answer());
    }

    // Pays the requested amount by solving challenges
    public void pay(double amount) throws IOException {
        double money = 0;
        while (money < amount) {
            Challenge challenge = fetchChallenge();
            ChallengeAnswer answer = solveChallenge(challenge);
            submitAnswer(answer);
            System.out.println("Current account balance: " + client.balance());
            money++;
        }
        System.out.println("Payment made: " + money);
    }
}
