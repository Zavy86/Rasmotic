#!/usr/bin/env python
import RPi.GPIO as GPIO
import time
import sys

# board pins
PIN32=32
PIN33=33
PIN35=35
PIN36=36
PIN37=37
PIN38=38
PIN40=40

# pin arguments
pin_args={'12':PIN32,
           '13':PIN33,
           '16':PIN36,
           '19':PIN35,
           '20':PIN38,
           '21':PIN40,
           '26':PIN37}

# action aguments
action_args={'0':GPIO.LOW,
             '1':GPIO.HIGH,
             'ON':GPIO.HIGH,
             'OFF':GPIO.LOW,
             'TRUE':GPIO.HIGH,
             'FALSE':GPIO.LOW}

# check arguments
if(len(sys.argv)==3):
	# check gpio pin
	if(sys.argv[1] in pin_args):
		pin=pin_args[sys.argv[1]]
	else:
		print 'error pin'
		sys.exit(1)
	# check action
	if(sys.argv[2] in action_args):
                action=action_args[sys.argv[2]]
        else:
                print 'error action'
		sys.exit(1)
else:
	# print usage
        print '{"error":"Usage: sudo ./relay.json.py GPIO[12|13|16|19|20|21|26] ACTION[ON|OFF]"}'
        sys.exit(1)


# setup gpio pin
GPIO.setwarnings(False)
GPIO.setmode(GPIO.BOARD)
GPIO.setup(pin,GPIO.OUT)

# relay status
relay_status={0:"FALSE",1:"TRUE"}

# get current status
status=GPIO.input(pin)

# check if action is different by status
if(status!=action):
	# change relay status
	GPIO.output(pin,action)
	# get currente status
	status=GPIO.input(pin)
	# check if changed
	if(status==action):
		# return new status
		print '{"status":'+str(relay_status[status])+',"changed":TRUE}'
	else:
		# error changing status
		print '{"error":"Error changing status"}'
else:
	# return current status
	print '{"status":'+str(relay_status[status])+',"changed":FALSE}'

# release resource
#GPIO.cleanup()

# exit
sys.exit(0)
