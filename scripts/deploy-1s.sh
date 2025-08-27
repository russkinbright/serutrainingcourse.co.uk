
# Run deploy.sh ~every second for ~1 minute
for i in $(seq 1 59); do
  /home/u715729948/domains/serutrainingcourse.co.uk/scripts/deploy.sh \
    >> /home/u715729948/domains/serutrainingcourse.co.uk/deploy.cron.log 2>&1
  sleep 1
done
