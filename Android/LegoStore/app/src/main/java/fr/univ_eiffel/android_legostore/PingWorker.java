package fr.univ_eiffel.android_legostore;

import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Build;
import androidx.annotation.NonNull;
import androidx.core.app.NotificationCompat;
import androidx.work.Worker;
import androidx.work.WorkerParameters;
import org.json.JSONObject;
import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;

public class PingWorker extends Worker {

    private static final String CHANNEL_ID = "LegoStoreChannel";

    public PingWorker(@NonNull Context context, @NonNull WorkerParameters workerParams) {
        super(context, workerParams);
    }

    @NonNull
    @Override
    public Result doWork() {
        SharedPreferences prefs = getApplicationContext().getSharedPreferences("AppPrefs", Context.MODE_PRIVATE);
        String deviceId = prefs.getString("device_id", "");
        String userId = prefs.getString("user_id", "");

        if (deviceId.isEmpty()) return Result.failure();

        try {
            URL url = new URL("http://10.0.2.2/SAELego/PHP/public/api_app_ping.php");
            HttpURLConnection conn = (HttpURLConnection) url.openConnection();
            conn.setRequestMethod("POST");
            conn.setRequestProperty("Content-Type", "application/json; utf-8");
            conn.setRequestProperty("Accept", "application/json");
            conn.setDoOutput(true);

            JSONObject jsonInput = new JSONObject();
            jsonInput.put("device_id", deviceId);
            if (!userId.isEmpty()) jsonInput.put("user_id", userId);

            try (OutputStream os = conn.getOutputStream()) {
                byte[] input = jsonInput.toString().getBytes("utf-8");
                os.write(input, 0, input.length);
            }

            BufferedReader br = new BufferedReader(new InputStreamReader(conn.getInputStream(), "utf-8"));
            StringBuilder response = new StringBuilder();
            String responseLine;
            while ((responseLine = br.readLine()) != null) {
                response.append(responseLine.trim());
            }

            JSONObject jsonResponse = new JSONObject(response.toString());

            if (jsonResponse.getBoolean("success")) {
                JSONObject notifications = jsonResponse.getJSONObject("notifications");

                if (notifications.has("daily_image") && prefs.getBoolean("daily_image_enabled", true)) {
                    JSONObject daily = notifications.getJSONObject("daily_image");
                    showNotification(daily.getInt("id"), daily.getString("title"), daily.getString("message"));
                }

                if (notifications.has("loyalty")) {
                    JSONObject loyalty = notifications.getJSONObject("loyalty");
                    showNotification(loyalty.getInt("id"), loyalty.getString("title"), loyalty.getString("message"));
                }
            }

            return Result.success();

        } catch (Exception e) {
            return Result.retry();
        }
    }

    private void showNotification(int id, String title, String message) {
        NotificationManager notificationManager = (NotificationManager) getApplicationContext().getSystemService(Context.NOTIFICATION_SERVICE);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            NotificationChannel channel = new NotificationChannel(CHANNEL_ID, "Lego Notifications", NotificationManager.IMPORTANCE_DEFAULT);
            notificationManager.createNotificationChannel(channel);
        }

        Intent intent = new Intent(getApplicationContext(), MainActivity.class);
        PendingIntent pendingIntent = PendingIntent.getActivity(getApplicationContext(), 0, intent, PendingIntent.FLAG_IMMUTABLE);

        NotificationCompat.Builder builder = new NotificationCompat.Builder(getApplicationContext(), CHANNEL_ID)
                .setSmallIcon(android.R.drawable.ic_dialog_info)
                .setContentTitle(title)
                .setContentText(message)
                .setPriority(NotificationCompat.PRIORITY_DEFAULT)
                .setContentIntent(pendingIntent)
                .setAutoCancel(true);

        notificationManager.notify(id, builder.build());
    }
}