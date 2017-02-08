#!/usr/bin/python
import sys
import Adafruit_DHT

# pin arguments
sensor_args={'11':Adafruit_DHT.DHT11,
             '22':Adafruit_DHT.DHT22,
             '2302':Adafruit_DHT.AM2302}

if len(sys.argv) == 3 and sys.argv[1] in sensor_args:
	sensor = sensor_args[sys.argv[1]]
	pin = sys.argv[2]
else:
	print '{"error":"Usage: sudo ./temphum.json.py [11|22|2302] GPIOpin"}'
	sys.exit(1)

# Try to grab a sensor reading.  Use the read_retry method which will retry up
# to 15 times to get a sensor reading (waiting 2 seconds between each retry).
humidity, temperature = Adafruit_DHT.read_retry(sensor, pin)

# Un-comment the line below to convert the temperature to Fahrenheit.
# temperature = temperature * 9/5.0 + 32

# check and return
if humidity is not None and temperature is not None:
	print '{{"temperature":{0:0.1f},"humidity":{1:0.1f}}}'.format(temperature,humidity)
else:
	print '{"error":"Failed to get reading"}'
	sys.exit(1)