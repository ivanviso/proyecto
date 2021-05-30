import flask
from flask import request, jsonify
 
app = flask.Flask(__name__)
app.config["DEBUG"] = True


@app.route('/', methods=['GET', 'POST'])
def home():
    if request.method == 'POST':
        return request.args
    if request.method == 'GET':
        return "as"
app.run()
