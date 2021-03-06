var React = require('react');
var MainStore = require('../../store/MainStore');
var mainActions = require('../../actions/mainActions');
var Utils = require('../../utils/Utils');
var _ = require('underscore');


function getSignUpState() {
	return {
		status: MainStore.getRegStatus(),
		chooseInput: '',
		departments: [],
		departmentId: 0
	}
}

var SignUp = React.createClass({
	getInitialState: function() {
		return getSignUpState();
	},

	handleRes: function(){
		this.state.email_error = false;
		this.state.pass_error = false;
		this.state.phone_error = false;
		this.state.regKey_error = false;
		this.state.department_error = false;

		email = React.findDOMNode(this.refs.email).value;
		password = React.findDOMNode(this.refs.pass).value;
		phone = React.findDOMNode(this.refs.phone).value;
		regKey = React.findDOMNode(this.refs.regKey).value;
		department = React.findDOMNode(this.refs.department).value;

		if (email == ''){
			this.setState({ email_error: true });
		}
		else if (password == ''){
			this.setState({ pass_error: true });
		}
		else if (phone == ''){
			this.setState({ phone_error: true });
		}
		else if (regKey == ''){
			this.setState({ regKey_error: true });
		}
		else if (department == ''){
			this.setState({ department_error: true });
		}
		else{
			var regData = {
				"email" 		: email,
				"password"  	: password,
				"first_name" 	: React.findDOMNode(this.refs.firstName).value,
				"last_name" 	: React.findDOMNode(this.refs.lastName).value,
				"phone"	 		: phone,
				'regKey' 		: regKey,
				'departmentId'	: this.state.departmentId,
				'departmentName': this.state.chooseInput
			}
			mainActions.signUp(regData);
		}
	},
	auth: function(){
		this.props.auth();
	},
	chooseDepartment: function(data){
        this.setState({
            departments: [],
            chooseInput: data.name,
            departmentId: data.id
        });
    },
	getDepartments: function(e){
		if(_.isEmpty(e.target.value)){
            this.setState({
                departments: []
            });
        }else{
            Utils.post({
		        url : 'search_departments',
		        data: {'key': e.target.value},
		        success: function(request){
		            if(request.status_code == 0){
		                this.setState({ departments: request.departments});
		            }
		        }.bind(this)
		    });
        }
		this.setState({chooseInput: event.target.value});
	},
	render: function() {
		 var searchedDepartments = function(department, index) {
            return (
                <SearchedDepartments department={ department } key={ index } onChoose={ this.chooseDepartment}/>
            );
        }.bind(this);
		if (this.state.status === 0){
			return(
				<div>
					<h3>Успешная регистраиця</h3>
					<span className="after-reg" onClick={ this.auth }>Авторизация</span>
				</div>
			)
		}
		else {
			return(
				<div>
					<div className="note">E-mail<em>*</em></div>
					<input className={ this.state.status == 1 || this.state.email_error ? 'error' : null } type="text" ref="email" />
					<div className="note error-note">{ this.state.status == 1 ? 'Такой email уже зарегистрирован' : null}</div>
	
					<div className="note">Пароль<em>*</em></div>
					<input className={ this.state.pass_error ? 'error' : null } type="password" ref="pass" />
	
					<div className="left-col">
						<div className="note">Имя</div>
						<input type="text" ref="firstName" />
					</div>
	
					<div className="right-col">
						<div className="note">Фамилия</div>
						<input type="text" ref="lastName" />
					</div>
	
					<div className="note">Телефон<em>*</em></div>
					<input className={ this.state.phone_error ? 'error' : null } type="text" ref="phone" />
	
					<div className="note">Код компании<em>*</em></div>
					<input className={ this.state.status == 2 || this.state.regKey_error ? 'error' : null } type="text" ref="regKey" />
					<div className="note error-note">{ this.state.status == 2 ? 'Неверный код регистрации' : null}</div>

					<div className="note">Отдел<em>*</em></div>
					<input className={ this.state.department_error ? 'error' : null } type="text" ref="department" onChange={ this.getDepartments } value={ this.state.chooseInput }/>
                    { this.state.departments.length > 0 ? <div className="autocomplete"><ul className="values">{ this.state.departments.map(searchedDepartments) }</ul></div> : null }
	
					<button onClick={this.handleRes}>Зарегистрироваться</button>
					<div className="note footnote">
						<em>*</em> - обязательные поля
					</div>
				</div>
			)
		}
	},

    // Add change listeners to stores
    componentDidMount: function() {
    	MainStore.addChangeRegListener(this._onChange);
    },

    // Remove change listers from stores
    componentWillUnmount: function() {
    	MainStore.removeChangeRegListener(this._onChange);
    },

    // Method to setState based upon Store changes
    _onChange: function() {
    	this.setState(getSignUpState());
    }
});

var SearchedDepartments = React.createClass({
    click: function(e){
        this.props.onChoose(e.target.dataset);
    },
    render: function() {
        var department = this.props.department;
        return (
            <li key={ this.props.key } onClick={ this.click }>
                <span data-id={ department.id } data-name={ department.name }>{ department.name }</span>
            </li>
        );
    }
});

function getState() {
    return {
        auth_error: false
    };
}

var SignIn = React.createClass({
	getInitialState: function() {
        return getState();
    },
	handleRes: function(e){
		e.preventDefault();
		var auth_data = {
			"email"	   : e.target.email.value,
			"password" : e.target.password.value,
		};

		var data;

    	Utils.post({
    	    url : 'check_auth',
    	    async :  true,
    	    data: { 'email' : auth_data.email, 'password' : auth_data.password },
    	    success: function(request){
    	    	data = request;
    	    }.bind(this)
    	});

     	if(data.status_code == 0){
    	    e.target.submit();
    	}
    	else{
    	    this.setState({ auth_error: true });
    	}
	},
	render: function() {
		return(
					<div className='sign-in'>
						<form action="/auth" method="post" onSubmit={this.handleRes}>
							<div className="note">E-mail:</div>
							<input autoComplete="on" type="text" name="email" />

							<div className="note">Пароль:</div>
							<input type="password" name="password" />

							{this.state.auth_error ? <div className='error'>Ошибка авторизации</div> : null}
							<input type='submit' value='Войти' />
						</form>
					</div>	
					
		)
	}
});

var AuthForm = React.createClass({
	getInitialState: function() {
    	return {
    		sign     : 'in',
    		signTitle: 'Создать аккаунт',
    		resBtn   : 'Войти'
    	};
  	},
  	handeSign: function(){
  		if(this.state.sign == 'in'){
  			this.setState({
	  			sign     : 'up',
	  			signTitle: 'У меня есть аккаунт',
	  			resBtn   : 'Регистрация'
  			});
  		}
  		else{
  			this.setState({
	  			sign     : 'in',
	  			signTitle: 'Создать аккаунт',
	  			resBtn   : 'Войти'
  			});
  		}	
  	},
  	closeForm: function(){
  		mainActions.closeForm();
  	},
	render: function() {
		return(
			<div>
				<div className='black-flow' onClick={this.closeForm}></div> 
				<div className ='pop-up sign-in-div'>
					<div className='auth-title' onClick={this.handeSign}>{this.state.signTitle}</div><div className='auth-title close-btn' onClick={this.closeForm}>Закрыть</div>
					{this.state.sign == 'in' ? <SignIn />:<SignUp auth={ this.handeSign } />}
				</div>
			</div>	
			
		)
	}
});

module.exports = AuthForm;