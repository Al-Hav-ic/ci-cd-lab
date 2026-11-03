<?php

declare(strict_types=1);

namespace App\Tasks;

final class Group
{
    /** @var array<int, Student> */
    private array $students = [];

    public function __construct(
        private string $groupId,
        private string $name,
    ) {}

    public function addStudent(Student $student): void
    {
        $this->students[] = $student;
    }

    public function removeStudent(string $id): bool
    {
        foreach ($this->students as $index => $student) {
            if ($student->getId() === $id) {
                unset($this->students[$index]);
                $this->students = array_values($this->students);

                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, Student>
     */
    public function searchStudent(string $query): array
    {
        return array_values(array_filter(
            $this->students,
            static fn (Student $student): bool => $student->matches($query),
        ));
    }

    /**
     * @return array<int, Student>
     */
    public function getStudents(): array
    {
        return $this->students;
    }
}
