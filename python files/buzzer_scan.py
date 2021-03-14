#Needed modules will be imported and configured.
import RPi.GPIO as GPIO
import time
GPIO.setmode(GPIO.BCM)

current_time = time.time()
time_delay1 = current_time + 0.2
time_delay2 = current_time + 0.4

GPIO_PIN = 23
GPIO.setup(GPIO_PIN, GPIO.OUT)

pwm = GPIO.PWM(GPIO_PIN, 500)
pwm.start(50)
while current_time < time_delay1:
    current_time = time.time()
else:
    pwm.ChangeFrequency(1500)
    
    
while current_time < time_delay2:
    current_time = time.time()
else:
    pwm.stop(50)

