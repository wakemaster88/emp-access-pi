import cv2
import os
import time
import subprocess
next_time = time.time()

time.sleep(10)
os.system('python /home/pi/Desktop/buzzer_startup_qr.py')

cap = cv2.VideoCapture(0)

detector = cv2.QRCodeDetector()

while True:
    current_time = time.time()
    _, img = cap.read()
    data, bbox, _ = detector.detectAndDecode(img)
    
    if(current_time >= next_time):
        
        if(bbox is not None):
            for i in range(len(bbox)):
                cv2.line(img, tuple(bbox[i][0]), tuple(bbox[(i+1) % len(bbox)][0]), color=(255,
                         0, 255), thickness=2)
            cv2.putText(img, data, (int(bbox[0][0][0]), int(bbox[0][0][1]) - 10), cv2.FONT_HERSHEY_SIMPLEX,
                        0.5, (0, 255, 0), 2)
            if data:
                print("data found: ", data)
                proc = subprocess.Popen("php /var/www/html/scan.php " + str(data), shell=True, stdout=subprocess.PIPE)
                script_response = proc.stdout.read()
                proc = subprocess.Popen("python /home/pi/Desktop/buzzer.py", shell=True, stdout=subprocess.PIPE)
                proc = subprocess.Popen("python /home/pi/Desktop/relais.py", shell=True, stdout=subprocess.PIPE)
                next_time = time.time() + 4   
        cv2.imshow("code detector", img)
        if(cv2.waitKey(1) == ord("q")):
            break
cap.release()
cv2.destroyAllWindows()