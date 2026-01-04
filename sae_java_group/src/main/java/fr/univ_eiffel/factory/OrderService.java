package fr.univ_eiffel.factory;

import com.google.gson.Gson;
import com.google.gson.JsonObject;

import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.Map;

public class OrderService {
    private static final String API_URL = "https://legofactory.plade.org";
    private final String email;
    private final String apiKey;
    private final Gson gson = new Gson();

    public OrderService(String email, String apiKey) { this.email = email; this.apiKey = apiKey; }

    public String requestQuote(Map<String, Integer> briques) {
        try {
            HttpURLConnection conn = createConn("/ordering/quote-request", "POST");
            try (OutputStream os = conn.getOutputStream()) { os.write(gson.toJson(briques).getBytes()); }
            if (conn.getResponseCode() == 200) {
                JsonObject response = gson.fromJson(new InputStreamReader(conn.getInputStream()), JsonObject.class);
                return response.get("id").getAsString();
            }
        } catch (Exception e) { e.printStackTrace(); }
        return null;
    }

    public boolean confirmOrder(String quoteId) {
        try {
            HttpURLConnection conn = createConn("/ordering/order/" + quoteId, "POST");
            conn.getOutputStream().write("{}".getBytes());
            return conn.getResponseCode() == 200;
        } catch (Exception e) { return false; }
    }

    private HttpURLConnection createConn(String path, String method) throws Exception {
        URL url = new URL(API_URL + path);
        HttpURLConnection conn = (HttpURLConnection) url.openConnection();
        conn.setRequestMethod(method);
        conn.setRequestProperty("X-Email", email);
        conn.setRequestProperty("X-Secret-Key", apiKey);
        conn.setRequestProperty("Accept", "application/json");
        if (method.equals("POST")) { conn.setRequestProperty("Content-Type", "application/json"); conn.setDoOutput(true); }
        return conn;
    }
}
