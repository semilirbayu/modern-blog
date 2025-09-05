export function isEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(email)
}

export function validateEmail(email) {
    if (!email) return 'Email is required'
    if (!isEmail(email)) return 'Please enter a valid email address'
    return null
}

export function validatePassword(password, minLength = 8) {
    if (!password) return 'Password is required'
    if (password.length < minLength) return `Password must be at least ${minLength} characters`
    return null
}

export function validatePasswordConfirmation(password, confirmation) {
    if (!confirmation) return 'Password confirmation is required'
    if (password !== confirmation) return 'Passwords do not match'
    return null
}

export function validateRequired(value, fieldName = 'Field') {
    if (!value || (typeof value === 'string' && !value.trim())) {
        return `${fieldName} is required`
    }
    return null
}

export function validateMinLength(value, minLength, fieldName = 'Field') {
    if (!value) return null
    if (value.length < minLength) {
        return `${fieldName} must be at least ${minLength} characters`
    }
    return null
}

export function validateMaxLength(value, maxLength, fieldName = 'Field') {
    if (!value) return null
    if (value.length > maxLength) {
        return `${fieldName} cannot exceed ${maxLength} characters`
    }
    return null
}

export function validateForm(fields, validators) {
    const errors = {}
    let isValid = true

    for (const [fieldName, fieldValue] of Object.entries(fields)) {
        const fieldValidators = validators[fieldName] || []
        
        for (const validator of fieldValidators) {
            const error = validator(fieldValue)
            if (error) {
                errors[fieldName] = error
                isValid = false
                break
            }
        }
    }

    return { isValid, errors }
}

export function getPasswordStrength(password) {
    if (!password) return { strength: 0, label: 'No password' }
    
    let score = 0
    const checks = {
        length: password.length >= 8,
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        numbers: /\d/.test(password),
        symbols: /[^A-Za-z0-9]/.test(password)
    }
    
    score = Object.values(checks).filter(Boolean).length
    
    const strengths = [
        { min: 0, max: 1, label: 'Very Weak', color: 'red' },
        { min: 2, max: 2, label: 'Weak', color: 'orange' },
        { min: 3, max: 3, label: 'Fair', color: 'yellow' },
        { min: 4, max: 4, label: 'Good', color: 'blue' },
        { min: 5, max: 5, label: 'Strong', color: 'green' }
    ]
    
    const strengthData = strengths.find(s => score >= s.min && score <= s.max)
    
    return {
        strength: score,
        label: strengthData.label,
        color: strengthData.color,
        checks
    }
}