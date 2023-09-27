'''
    # A simple example of reading data from mysql database and post to TSDSystem using Python 
    # @author Carmelo Cassisi
'''

import mysql.connector
import json
import requests

# You can save a configuration into a separate file (JSON) and use the following function (read_json_config):
def read_json_config(config_file_name):
    with open(config_file_name, "r") as config_json_file:
        try:
            config = json.load(config_json_file)
            print("Configuration read successful", end="")
            return config
        except:
            print("Error on reading configuration file", end="")
            return None

# Hard-coded configuration (example, you can choose any other structure)      
config = {
    "server_url": "http://localhost/tsdws/",
    "auth": {
        "token": "<mytoken>"
    }
}
# or use the following (uncomment)
# config = read_json_config("config_file_name")

# Timeseries info (from TSDSystem registration) --> you can choose to put them into the config structure
ts_id = "a22df213-c5a3-41f2-8f21-1a9bb47ce871" # for example, this timeseries has only 1 column for value (double precision) named "value"
ts_cols = ["time", "value"] # remember that the name of the timestamp columns is always "time" into TSDSystem tables!

# connect to mysql to retrieve data to post into TSDSystem
mydb = mysql.connector.connect(
  host="localhost",
  user="yourusername",
  password="yourpassword",
  database="mydatabase"
)
mycursor = mydb.cursor()
mycursor.execute("SELECT x.time_column, x.value_column FROM mytable x") # in this example x.time_column is a field of type date/datetime, and x.value_column is a field of numeric type
myresult = mycursor.fetchall()

# uncomment the following loop to check result (see that time column is automatically converted into datetime.datetime object)
# for x in myresult:
#  print(x)

# prepare data
post_data = []
for x in myresult:
  post_data.append([str(x[0]), x[1]]) # force the first column (time) conversion to string 

# preparing to post data 
url = config["server_url"].rstrip('/') + '/timeseries/' + ts_id + '/values'
headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
    "Authorization": config["auth"]["token"]
}
payload = {
    "columns": ts_cols,
    "data": post_data
} 

# make post
try:
    print("Calling request to " + url, end="\n")
    response = requests.post(url, json=payload, headers=headers, verify=False)
    print(response, end="\n")
    response_obj = json.loads(response.text)
    if (response.status_code in (200, 201, 202, 207)):
        print(response_obj["data"], end="\n")
        print("All is fine!", end="\n")
    else:
        print("Error: " + response_obj["error"], end="\n")
except:
    print("An error occurred on post values request", end="\n")