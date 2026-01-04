package fr.univ_eiffel.factory;

import com.google.gson.Gson;
import com.google.gson.JsonObject;

import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.security.MessageDigest;

public class BillingService {
    private static final String API_URL = "https://legofactory.plade.org";
    private final String email;
    private final String apiKey;
    private final Gson gson = new Gson();

    public BillingService(String email, String apiKey) {
        this.email = email;
        this.apiKey = apiKey;
    }

    public double rechargeAccount() {
        try {
            HttpURLConnection conn = createConn("/billing/challenge", "GET");
            if (conn.getResponseCode() != 200) return 0;
            JsonObject challenge = gson.fromJson(new InputStreamReader(conn.getInputStream()), JsonObject.class);
            String data = challenge.get("data_prefix").getAsString();
            String hash = challenge.get("hash_prefix").getAsString();
            double reward = challenge.get("reward").getAsDouble();
            String solution = solve(data, hash);
            return (solution != null) ? sendSolution(data, hash, solution, reward) : 0;
        } catch (Exception e) { return 0; }
    }

    private String solve(String data, String hashPrefix) throws Exception {
        MessageDigest digest = MessageDigest.getInstance("SHA-256");
        long nonce = 0;
        while (nonce < 10_000_000) {
            String candidate = data + Long.toHexString(nonce);
            byte[] h = digest.digest(hexToBytes(candidate));
            if (bytesToHex(h).startsWith(hashPrefix)) return candidate;
            nonce++;
        }
        return null;
    }

    private double sendSolution(String d, String h, String ans, double reward) throws Exception {
        JsonObject json = new JsonObject();
        json.addProperty("data_prefix", d); json.addProperty("hash_prefix", h); json.addProperty("answer", ans);
        HttpURLConnection conn = createConn("/billing/challenge-answer", "POST");
        try (OutputStream os = conn.getOutputStream()) { os.write(gson.toJson(json).getBytes()); }
        return (conn.getResponseCode() == 200) ? reward : 0;
    }

    private HttpURLConnection createConn(String path, String method) throws Exception {
        URL url = new URL(API_URL + path);
        HttpURLConnection conn = (HttpURLConnection) url.openConnection();
        conn.setRequestMethod(method);
        conn.setRequestProperty("X-Email", email);
        conn.setRequestProperty("X-Secret-Key", apiKey);
        if (method.equals("POST")) { conn.setRequestProperty("Content-Type", "application/json"); conn.setDoOutput(true); }
        return conn;
    }

    private byte[] hexToBytes(String s) {
        if(s.length() % 2 != 0) s = "0" + s;
        byte[] data = new byte[s.length() / 2];
        for (int i = 0; i < s.length(); i += 2) data[i / 2] = (byte) ((Character.digit(s.charAt(i), 16) << 4) + Character.digit(s.charAt(i+1), 16));
        return data;
    }
    private String bytesToHex(byte[] bytes) {
        StringBuilder sb = new StringBuilder();
        for (byte b : bytes) sb.append(String.format("%02x", b));
        return sb.toString();
    }
}