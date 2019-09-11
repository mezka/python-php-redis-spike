import redis
import subprocess

r = redis.StrictRedis()

p = subprocess.Popen(["php", "worker.php"])

pubsub = r.pubsub()
pubsub.subscribe("php_python")


for item in pubsub.listen():
    if item['type'] == 'message' and item['data'] == b'LOGIN':
        print("PYTHON: Login was succesful ...")
        pubsub.unsubscribe()
    elif item['type'] == 'message' and item['data'] == b'CHALLENGE':
        pubsub.unsubscribe()
        
        print("PYTHON: Challenge is required ...")

        pubsub = r.pubsub()
        pubsub.subscribe("php_python")

        r.publish('python-php', input("PYTHON: Input verification code: "))

        for item in pubsub.listen():
            if item['type'] == 'message' and item['data'] == b'SUCCESS':
                print("PYTHON: Challenge fulfilled ...")
                pubsub.unsubscribe()
            elif item['type'] == 'message' and item['data'] == b'FAILURE':
                print("PYTHON: Challenge failed ...")
                pubsub.unsubscribe()
        



        
