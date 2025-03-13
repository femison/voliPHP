class Datepicker {
    constructor(input) {
        this.input = input;
        this.today = new Date(); // Текущая дата (13 марта 2025)
        this.maxDate = new Date(this.today); // Максимальная дата (16 лет назад)
        this.maxDate.setFullYear(this.today.getFullYear() - 16);
        this.date = new Date(this.maxDate); // Начальная дата = 16 лет назад
        this.viewDate = new Date(this.maxDate); // Дата для отображения
        this.selectedDate = null;
        this.isOpen = false;
        this.viewMode = 'days'; // Режим отображения: 'days' или 'months'
        this.init();
    }

    init() {
        this.createPicker();
        this.input.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle();
        });
        document.addEventListener('click', (e) => this.handleOutsideClick(e));

        // Привязываем события к кнопкам один раз при создании
        this.prevButton = this.picker.querySelector('.prev');
        this.nextButton = this.picker.querySelector('.next');
        this.title = this.picker.querySelector('.title');
        this.prevButton.addEventListener('click', (e) => {
            e.stopPropagation();
            this.prev();
        });
        this.nextButton.addEventListener('click', (e) => {
            e.stopPropagation();
            this.next();
        });
        this.title.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleViewMode();
        });
    }

    createPicker() {
        this.picker = document.createElement('div');
        this.picker.className = 'datepicker';
        this.picker.innerHTML = `
            <div class="datepicker-header">
                <button class="prev"><</button>
                <span class="title"></span>
                <button class="next">></button>
            </div>
            <div class="datepicker-body"></div>
        `;
        document.body.appendChild(this.picker);
        this.render();
    }

    toggle() {
        this.isOpen = !this.isOpen;
        this.render();
        if (this.isOpen) this.position();
    }

    position() {
        const rect = this.input.getBoundingClientRect();
        this.picker.style.top = `${rect.bottom + window.scrollY + 5}px`;
        this.picker.style.left = `${rect.left + window.scrollX}px`;
    }

    handleOutsideClick(e) {
        if (!this.picker.contains(e.target) && e.target !== this.input && this.isOpen) {
            this.isOpen = false;
            this.render();
        }
    }

    toggleViewMode() {
        this.viewMode = this.viewMode === 'days' ? 'months' : 'days';
        this.render();
    }

    render() {
        if (!this.isOpen) {
            this.picker.style.display = 'none';
            return;
        }
        this.picker.style.display = 'block';

        this.title.textContent = this.viewMode === 'days'
            ? this.viewDate.toLocaleString('ru', { month: 'long', year: 'numeric' })
            : this.viewDate.getFullYear();

        this.picker.querySelector('.datepicker-body').innerHTML = this.viewMode === 'days'
            ? this.renderDays()
            : this.renderMonths();

        if (this.viewMode === 'days') {
            this.picker.querySelectorAll('.day').forEach(day => {
                day.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.selectDay(day.dataset.day);
                });
            });
        } else {
            this.picker.querySelectorAll('.month').forEach(month => {
                month.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.selectMonth(month.dataset.month);
                });
            });
        }

        const nextStep = new Date(this.viewDate.getFullYear(), this.viewMode === 'days' ? this.viewDate.getMonth() + 1 : 0, 1);
        this.nextButton.disabled = nextStep > this.maxDate;
        this.prevButton.disabled = false; // Нет ограничения назад
    }

    renderDays() {
        const year = this.viewDate.getFullYear();
        const month = this.viewDate.getMonth();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const firstDay = new Date(year, month, 1).getDay();
        const offset = firstDay === 0 ? 6 : firstDay - 1;

        let html = '<div class="day-names">';
        const dayNames = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
        dayNames.forEach(name => html += `<span>${name}</span>`);
        html += '</div><div class="days">';

        for (let i = 0; i < offset; i++) {
            html += '<span></span>';
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const isFuture = date > this.maxDate;
            const isSelected = this.selectedDate && 
                this.selectedDate.getFullYear() === year && 
                this.selectedDate.getMonth() === month && 
                this.selectedDate.getDate() === day;
            const isWeekend = date.getDay() === 0 || date.getDay() === 6;
            html += `
                <span class="day${isWeekend ? ' weekend' : ''}${isSelected ? ' selected' : ''}${isFuture ? ' disabled' : ''}" 
                      data-day="${day}" 
                      ${isFuture ? 'style="pointer-events: none; color: #ccc;"' : ''}>
                    ${day}
                </span>
            `;
        }
        html += '</div>';
        return html;
    }

    renderMonths() {
        const year = this.viewDate.getFullYear();
        const months = [
            'Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн',
            'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'
        ];
        let html = '<div class="months">';

        months.forEach((monthName, index) => {
            const monthDate = new Date(year, index, 1);
            const isFuture = monthDate > this.maxDate;
            const isSelected = this.selectedDate && 
                this.selectedDate.getFullYear() === year && 
                this.selectedDate.getMonth() === index;
            html += `
                <span class="month${isSelected ? ' selected' : ''}${isFuture ? ' disabled' : ''}" 
                      data-month="${index}" 
                      ${isFuture ? 'style="pointer-events: none; color: #ccc;"' : ''}>
                    ${monthName}
                </span>
            `;
        });
        html += '</div>';
        return html;
    }

    prev() {
        if (this.viewMode === 'days') {
            this.viewDate.setMonth(this.viewDate.getMonth() - 1);
        } else {
            this.viewDate.setFullYear(this.viewDate.getFullYear() - 1);
        }
        this.render();
    }

    next() {
        const nextStep = new Date(this.viewDate.getFullYear(), this.viewMode === 'days' ? this.viewDate.getMonth() + 1 : 0, 1);
        if (nextStep <= this.maxDate) {
            if (this.viewMode === 'days') {
                this.viewDate.setMonth(this.viewDate.getMonth() + 1);
            } else {
                this.viewDate.setFullYear(this.viewDate.getFullYear() + 1);
            }
            this.render();
        }
    }

    selectDay(day) {
        this.selectedDate = new Date(this.viewDate.getFullYear(), this.viewDate.getMonth(), day);
        // Форматируем дату вручную для локального времени
        const year = this.selectedDate.getFullYear();
        const month = String(this.selectedDate.getMonth() + 1).padStart(2, '0'); // +1, так как месяцы начинаются с 0
        const dayFormatted = String(this.selectedDate.getDate()).padStart(2, '0');
        const formattedDate = `${year}-${month}-${dayFormatted}`;
        this.input.value = formattedDate;
        this.isOpen = false;
        this.render();
    }

    selectMonth(month) {
        this.viewDate.setMonth(parseInt(month));
        this.viewMode = 'days';
        this.render();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.datepicker-here').forEach(input => new Datepicker(input));
});