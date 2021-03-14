import RPi.GPIO as GPIO
import time
GPIO.setmode(GPIO.BCM) # GPIO Nummern statt Board Nummern
current_time = time.time()
time_delay = current_time + 4
 
RELAIS_1_GPIO = 24
GPIO.setup(RELAIS_1_GPIO, GPIO.OUT) # GPIO Modus zuweisen
GPIO.output(RELAIS_1_GPIO, GPIO.HIGH) # an

while current_time < time_delay:
    current_time = time.time()
else:
    GPIO.output(RELAIS_1_GPIO, GPIO.LOW) # aus
    
    
